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
use App\Dto\Auth\RegisterInput;

#[Route('/api/update-password', name: 'app_update_password', methods: ['PATCH'])]
class UpdatePasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {
        /*if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);
            return new JsonResponse([
                'success' => false,
                'message' => 'update.password.error.token.found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.password.error.token.not.found'
            ], Response::HTTP_BAD_REQUEST);
        }*/
        try{
            $data = $request->getPayload()->all();
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'update.password.error.request'
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
                'message' => 'update.password.error.invalid.token',
                'error' => $e->getReason()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $dto = new RegisterInput();
        $dto->password = $data['password'] ?? null;
        $dto->email = $user->getEmail();

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $v->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        $user->setPassword($userPasswordHasher->hashPassword($user, $dto->password));
        $em->persist($user);
        $em->flush();

        // The token is valid; allow the user to change their password.
        return new JsonResponse([
            'success' => true,
            'message' => 'update.password.success'
        ], Response::HTTP_OK);
    }
}
