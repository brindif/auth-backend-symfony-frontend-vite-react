<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\RegistrationController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\RegisterInput;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/api/register',
            controller: RegistrationController::class,
            read: false,
            input: RegisterInput::class,
            output: false,
            name: 'api_register',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class Register
{

}
