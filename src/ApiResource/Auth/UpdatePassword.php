<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Auth\UpdatePasswordController;
use App\Dto\Auth\UpdatePasswordInput;

#[ApiResource(
    operations: [
        new Patch(
            uriTemplate: '/update-password',
            controller: UpdatePasswordController::class,
            read: false,
            input: UpdatePasswordInput::class,
            output: false,
            name: 'app_update_password',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class UpdatePassword
{

}
