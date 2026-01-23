<?php

namespace App\Controller\Auth;

use App\Entity\Auth\User;
use App\Security\Auth\EmailVerifier;
use App\Repository\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Dto\Auth\VerifyEmailInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/verify/email', name: 'api_verify_email', methods: ['GET'])]
class VerifyEmailController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    //public function verifyUserEmail(Request $request): Response
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator): Response
    {

        $dto = new VerifyEmailInput();
        $dto->token = $request->query->get('token') ?? null;
        $dto->expires = $request->query->get('expires') ?? null;
        $dto->id = $request->query->get('id') ?? null;
        $dto->signature = $request->query->get('signature') ?? null;

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return $this->json(
                    ['success' => false, 'error' => $v->getMessage()],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        /** @var User $user */
        $id = $request->query->get('id');
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->json(
                ['success' => false, 'error' => 'api.verify.email.no_id'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = $userRepository->find($id);

        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->json(
                ['success' => false, 'error' => 'api.verify.email.bad_id'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json(
              ['success' => false, 'error' => 'api.verify.email.error', 'debug' => $exception->getReason()],
              Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->json(
          ['success' => true],
          Response::HTTP_OK
        );
    }
}
