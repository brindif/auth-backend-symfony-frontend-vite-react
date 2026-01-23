<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Auth\CheckEmailController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\CheckEmailInput;
use ApiPlatform\OpenApi\Model\Parameter;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/check-email',
            controller: CheckEmailController::class,
            read: false,
            input: CheckEmailInput::class,
            output: false,
            name: 'app_check_email',
            openapi: new Operation(
                tags: ['Auth'],
                parameters: [
                    new Parameter(
                        name: 'token',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'string'],
                        description: 'Token'
                    ),
                    new Parameter(
                        name: 'expires',
                        in: 'query',
                        required: false,
                        schema: ['type' => 'string'],
                        description: 'timestamp of link expiration'
                    ),
                ]
            )
        ),
    ]
)]
final class CheckEmail
{

}
