<?php

namespace App\ApiResource\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Auth\User as UserEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;

#[ApiResource(
    shortName: 'User',
    openapi: new Operation(tags: ['User']),
    stateOptions: new Options(entityClass: UserEntity::class),
    operations: [
        new Post(
            uriTemplate: '/user',
            name: 'api_user_post',
            security: "is_granted('ROLE_ADMIN', object)"
        ),
        new GetCollection(
            uriTemplate: '/users',
            name: 'api_user_collection',
        ),
        new Get(
            uriTemplate: '/user/{id}',
            name: 'api_user_get',
        ),
        new Put(
            uriTemplate: '/user/{id}',
            name: 'api_user_put',
            security: "is_granted('ROLE_ADMIN', object)"
        ),
        new Patch(
            uriTemplate: '/user/{id}',
            name: 'api_user_patch',
            security: "is_granted('ROLE_ADMIN', object)"
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            name: 'api_user_delete',
            security: "is_granted('ROLE_ADMIN', object)"
        ),
    ]
)]

#[Map(source: UserEntity::class)]
final class User
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[ApiProperty(readable: false, writable: true)]
    public ?string $iri = null;

    public ?string $name = null;

    public ?string $email = null;

    private ?array $roles = null;

    private bool $isVerified = false;
}