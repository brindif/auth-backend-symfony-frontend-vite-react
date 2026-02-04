<?php

namespace App\ApiResource\Page;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Page\TabPostInput;
use App\Dto\Page\TabPutInput;
use App\State\Page\TabPostProcessor;
use App\State\Page\TabPutProcessor;
use App\Entity\Page\Tab as TabEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\Enum\TabTypeEnum;
use App\Enum\PermissionEnum;
use ApiPlatform\Metadata\ApiProperty;

#[ApiResource(
    shortName: 'Tab',
    openapi: new Operation(tags: ['Tab']),
    stateOptions: new Options(entityClass: TabEntity::class),
    operations: [
        new Post(
            uriTemplate: '/tab',
            name: 'api_tab_post',
            processor: TabPostProcessor::class,
            input: TabPostInput::class,
        ),
        new GetCollection(
            uriTemplate: '/tabs',
            name: 'api_tab_collection',
        ),
        new Get(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_get',
        ),
        new Put(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_put',
            processor: TabPutProcessor::class,
            input: TabPutInput::class,
            security: "is_granted('manage', object)"
        ),
        new Patch(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_patch',
            security: "is_granted('manage', object)"
        ),
        new Delete(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_delete',
            security: "is_granted('manage', object)"
        ),
    ]
)]

#[Map(source: TabEntity::class)]
final class Tab
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[ApiProperty(readable: false, writable: true)]
    public ?string $iri = null;

    public ?string $name = null;

    public ?string $nameDefault = null;

    public ?string $route = null;

    public ?int $position = null;

    public ?TabEntity $parent = null;

    public ?TabTypeEnum $type = null;

    public ?PermissionEnum $permission = null;

    public ?iterable $permissions = [];
}