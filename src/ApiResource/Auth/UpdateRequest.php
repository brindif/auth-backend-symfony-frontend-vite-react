<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\UpdateRequestController;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Auth\UpdateRequestInput;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/me/request',
            controller: UpdateRequestController::class,
            read: false,
            input: UpdateRequestInput::class,
            output: false,
            name: 'app_update_request',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class UpdateRequest
{

}
