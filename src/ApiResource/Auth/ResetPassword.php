<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Controller\Auth\ResetPasswordController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\ResetPasswordInput;

#[ApiResource(
    operations: [
        new Patch(
            uriTemplate: '/reset-password',
            controller: ResetPasswordController::class,
            read: false,
            input: ResetPasswordInput::class,
            output: false,
            name: 'app_reset_password',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class ResetPassword
{

}
