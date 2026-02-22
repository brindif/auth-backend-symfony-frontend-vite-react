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
use DateTimeInterface;
use App\Dto\Auth\UserPutInput;
use App\State\Auth\UserPutProcessor;
use App\State\Auth\UserDeleteProcessor;

#[ApiResource(
    shortName: 'User',
    openapi: new Operation(tags: ['User']),
    stateOptions: new Options(entityClass: UserEntity::class),
    operations: [
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
            processor: UserPutProcessor::class,
            input: UserPutInput::class,
            security: "is_granted('ROLE_ADMIN', object)"
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            name: 'api_user_delete',
            read: false,
            output: false,
            processor: UserDeleteProcessor::class,
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

    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    public ?array $roles = null;

    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    public bool $isVerified = false;

    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    public ?DateTimeInterface $createdAt = null;

    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    public ?DateTimeInterface $updatedAt = null;

    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    public ?string $updatedBy = null;
}