<?php

namespace App\Controller\Auth;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Dto\Auth\UpdateWithTokenInput;
use App\Repository\Auth\CheckEmailRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[Route('/api/me/token', name: 'api_update_with_token', methods: ['PATCH'])]
class UpdateWithTokenController extends AbstractController {
    use ResetPasswordControllerTrait;
    
    private const SELECTOR_LENGTH = 20;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $em,
        private CheckEmailRepository $repository,
        private MailerInterface $mailer,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        #[CurrentUser] ?User $currentUser,
    ): JsonResponse {
        try{
            $data = $request->getPayload()->all();
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.request'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var string $email */
        $token = $data['token'] ?? null;

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.invalid.token',
                'error' => $e->getReason()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Get CheckEmail instance and add email and token in database
        $selector = substr($token, 0, self::SELECTOR_LENGTH);
        $resetRequest = $this->repository->findResetPasswordRequest($selector);
        $checkEmail = $this->repository->findOneByToken($resetRequest->getHashedToken());
        if (empty($checkEmail)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.unknow.token',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $dto = new UpdateWithTokenInput();
        $dto->password = $data['password'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->token = $token;

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $v->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if ($checkEmail->getType() === "password") {
            // Update password
            $user->setPassword($userPasswordHasher->hashPassword($user, $dto->password));

            $violations = $this->validator->validate($user);
            if (count($violations) > 0) {
                foreach ($violations as $v) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => $v->getMessage()
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $this->em->persist($user);
            $this->em->flush();
        } elseif (
            $currentUser &&
            $checkEmail->getType() === "email" &&
            $checkEmail->getEmail() !== $currentUser->getEmail()
        ) {
            // Update email
            $currentUser->setEmail($checkEmail->getEmail());
            $this->em->persist($currentUser);
            $this->em->flush();
            $response = new JsonResponse(['message' => 'logout.success'], Response::HTTP_OK);
            // Delete JWT cookie
            $response->headers->clearCookie('BEARER', '/', null, true, true, 'lax');
            // Delete gesdinet cookie
            $response->headers->clearCookie('refresh_token', '/', null, true, true, 'lax');
            return $response;
        } elseif (
            $currentUser &&
            !empty($dto->email) &&
            $checkEmail->getType() === "email" &&
            $checkEmail->getEmail() === $currentUser->getEmail() &&
            $dto->email !== $currentUser->getEmail()
        ) {
            // Create token and send email
            return $this->sendEmail($currentUser, $dto->email);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.unknow'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // The token is valid; allow the user to change their password.
        return new JsonResponse([
            'success' => true,
            'message' => 'update.with.token.success'
        ], Response::HTTP_OK);
    }

    private function sendEmail($user, $newEmail): JsonResponse {
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.reset.token'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Get CheckEmail instance and add email and token in database
        $selector = substr($resetToken->getToken(), 0, self::SELECTOR_LENGTH);
        $resetRequest = $this->repository->findResetPasswordRequest($selector);
        $checkEmail = $this->repository->findOneByToken($resetRequest->getHashedToken());
        if (!empty($checkEmail)) {
            $checkEmail->setEmail($newEmail);
            $checkEmail->setType("email");
            $this->em->persist($checkEmail);
            $this->em->flush();
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.invalid.token'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $resetUrl = rtrim($this->getParameter('mailer.frontend')).
                '/validate-update-email?'.
                http_build_query(['token' => $resetToken->getToken()], '', '&', PHP_QUERY_RFC3986);;

            $email = (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('mailer.email'),
                    $this->getParameter('mailer.sender')
                ))
                ->to((string) $newEmail)
                ->subject($this->getParameter('mailer.subject'))
                ->htmlTemplate('reset_password/new_email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'resetUrl' => $resetUrl,
                ])
            ;

            $this->mailer->send($email);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.with.token.error.send.mail'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'update.with.token.check.email'
        ], Response::HTTP_OK);
    }
}