<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Controller\Auth\PatchCurrentUserController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\PatchCurrentUserInput;

#[ApiResource(
    operations: [
        new Patch(
            uriTemplate: '/api/me',
            controller: PatchCurrentUserController::class,
            read: false,
            input: PatchCurrentUserInput::class,
            output: false,
            name: 'api_patch_current_user',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class PatchCurrentUser
{

}
