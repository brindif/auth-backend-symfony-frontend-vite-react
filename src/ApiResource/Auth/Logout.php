<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\LogoutController;
use ApiPlatform\OpenApi\Model\Operation;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/api/logout',
            controller: LogoutController::class,
            read: false,
            output: false,
            name: 'api_logout',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class Logout
{

}
