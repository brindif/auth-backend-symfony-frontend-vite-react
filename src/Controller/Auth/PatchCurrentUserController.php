<?php

namespace App\Controller\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Auth\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api/me', name: 'api_patch_current_user', methods: ['PATCH'])]
final class PatchCurrentUserController
{
    public function __invoke(
        #[CurrentUser] ?User $user,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'profile.error.no.user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try{
            $data = $request->toArray();
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'profile.error.request'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->setName($data['name'] ?? null);

        $violations = $validator->validate($user);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $v->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'profile.success',
        ], Response::HTTP_OK);
    }
}
