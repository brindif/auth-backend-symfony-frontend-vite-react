<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Auth\VerifyEmailController;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/api/verify/email',
            controller: VerifyEmailController::class,
            read: false,
            output: false,
            name: 'api_verify_email',
            openapi: new Operation(
                tags: ['Auth'],
                parameters: [
                    new Parameter(
                        name: 'expires',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'string'],
                        description: 'timestamp of link expiration'
                    ),
                    new Parameter(
                        name: 'signature',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'string'],
                        description: 'Link signature'
                    ),
                    new Parameter(
                        name: 'token',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'string'],
                        description: 'Token'
                    ),
                    new Parameter(
                        name: 'id',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'integer'],
                        description: 'Id of user'
                    )
                ]
            )
        ),
    ]
)]
final class VerifyEmail
{
}
