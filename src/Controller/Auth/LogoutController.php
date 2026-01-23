<?php

namespace App\Controller\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
final class LogoutController
{
    public function __invoke(): JsonResponse
    {
        $response = new JsonResponse(['message' => 'logout.success'], Response::HTTP_OK);

        // Delete JWT cookie
        $response->headers->clearCookie('BEARER', '/', null, true, true, 'lax');

        // Delete gesdinet cookie
        $response->headers->clearCookie('refresh_token', '/', null, true, true, 'lax');

        return $response;
    }
}
