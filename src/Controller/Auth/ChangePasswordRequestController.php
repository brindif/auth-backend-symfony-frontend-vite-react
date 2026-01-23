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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/change-password-request', name: 'app_change_password_request', methods: ['POST'])]
class ChangePasswordRequestController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    public function __invoke(Request $request, MailerInterface $mailer): JsonResponse
    {
        try{
            $data = $request->getPayload()->all();
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'change.password.request.error.request'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var string $email */
        $email = $data['email'] ?? null;

        return $this->processSendingPasswordResetEmail($email, $mailer);
    }

    private function processSendingPasswordResetEmail(
        string $email,
        MailerInterface $mailer
    ): JsonResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'change.password.request.error.email.not.found'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'change.password.request.error.reset.token'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $resetUrl = rtrim($this->getParameter('mailer.frontend')).
                '/update-password?'.
                http_build_query(['token' => $resetToken->getToken()], '', '&', PHP_QUERY_RFC3986);;

            $email = (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('mailer.email'),
                    $this->getParameter('mailer.sender')
                ))
                ->to((string) $user->getEmail())
                ->subject($this->getParameter('mailer.subject'))
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'resetUrl' => $resetUrl,
                ])
            ;

            $mailer->send($email);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'change.password.request.error.send.mail'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return new JsonResponse([
            'success' => true,
            'message' => 'change.password.request.success'
        ], Response::HTTP_OK);
    }
}
