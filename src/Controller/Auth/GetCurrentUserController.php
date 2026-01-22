<?php

namespace App\Controller\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Auth\User;

final class GetCurrentUserController
{
    #[Route('/api/me', name: 'api_get_current_user', methods: ['GET'])]
    public function __invoke(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'current.user.error.unknow',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'current.user.success',
            'user' => [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], Response::HTTP_OK);
    }
}
