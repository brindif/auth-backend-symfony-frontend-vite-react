<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\ChangePasswordRequestController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\ChangePasswordRequestInput;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/change-password-request',
            controller: ChangePasswordRequestController::class,
            read: false,
            input: ChangePasswordRequestInput::class,
            output: false,
            name: 'app_change_password_request',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class ChangePasswordRequest
{

}
