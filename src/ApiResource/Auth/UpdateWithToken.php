<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Auth\UpdateWithTokenController;
use App\Dto\Auth\UpdateWithTokenInput;

#[ApiResource(
    operations: [
        new Patch(
            uriTemplate: '/me/token',
            controller: UpdateWithTokenController::class,
            read: false,
            input: UpdateWithTokenInput::class,
            output: false,
            name: 'app_update_with_token',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class UpdateWithToken
{

}
