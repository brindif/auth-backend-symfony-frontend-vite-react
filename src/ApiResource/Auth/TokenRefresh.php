<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\RefreshTokenControler;
use ApiPlatform\OpenApi\Model\Operation;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/api/token/refresh',
            controller: RefreshTokenControler::class,
            read: false,
            output: false,
            name: 'gesdinet_jwt_refresh_token',
            openapi: new Operation(tags: ['Auth'])
        ),
    ]
)]
final class TokenRefresh
{
}
