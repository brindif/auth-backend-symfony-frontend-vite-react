<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class RefreshTokenControler extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->json(
            ['message' => 'Not implemented (documentation only).'],
            Response::HTTP_NOT_IMPLEMENTED
        );
    }
}
