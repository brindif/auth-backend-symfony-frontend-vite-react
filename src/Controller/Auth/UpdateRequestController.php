<?php

namespace App\Controller\Auth;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Dto\Auth\UpdateRequestInput;
use App\Repository\Auth\CheckEmailRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/me/request', name: 'api_update_request', methods: ['POST'])]
class UpdateRequestController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const TYPES = ['password', 'email'];
    private const SELECTOR_LENGTH = 20;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    public function __invoke(
        Request $request,
        MailerInterface $mailer,
        ValidatorInterface $validator,
        CheckEmailRepository $repository,
        #[CurrentUser] ?User $currentUser
    ): JsonResponse {
        try{
            $data = $request->getPayload()->all();
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.request'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var string $email */
        $email = $data['email'] ?? null;

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.unknow.user'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $dto = new UpdateRequestInput();
        $dto->type = $data["type"] ?? "";
        // Get authentified user email when aked for email update
        if ($dto->type === 'email' && empty($currentUser)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.unknow.user'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $dto->email = $dto->type === "email" ? $currentUser->getEmail() : $email;

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $v->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.reset.token'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Get CheckEmail instance and add email and token in database
        $selector = substr($resetToken->getToken(), 0, self::SELECTOR_LENGTH);
        $resetRequest = $repository->findResetPasswordRequest($selector);
        $checkEmail = $repository->findOneByToken($resetRequest->getHashedToken());
        if (!empty($checkEmail)) {
            $checkEmail->setEmail($dto->email);
            $checkEmail->setType($dto->type);
            $this->entityManager->persist($checkEmail);
            $this->entityManager->flush();
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.token.undefined'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $resetUrl = rtrim($this->getParameter('mailer.frontend')).
                '/update-'.$dto->type.'?'.
                http_build_query(['token' => $resetToken->getToken()], '', '&', PHP_QUERY_RFC3986);;

            $email = (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('mailer.email'),
                    $this->getParameter('mailer.sender')
                ))
                ->to((string) $user->getEmail())
                ->subject($this->getParameter('mailer.subject'))
                ->htmlTemplate('reset_password/change_'.$dto->type.'.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'resetUrl' => $resetUrl,
                ])
            ;

            $mailer->send($email);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.request.error.send.mail'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'update.request.success'
        ], Response::HTTP_OK);
    }
}
