<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Auth\GetCurrentUserController;
use ApiPlatform\OpenApi\Model\Operation;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/api/me',
            controller: GetCurrentUserController::class,
            read: false,
            output: false,
            name: 'api_get_current_user',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class GetCurrentUser
{

}
