<?php

namespace App\ApiResource\Page;

use App\Entity\Page\Tab as TabEntity;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Page\TabPostInput;
use ApiPlatform\Doctrine\Orm\State\Options;

#[ApiResource(
    shortName: 'Tab',
    stateOptions: new Options(entityClass: TabEntity::class),
    openapi: new Operation(tags: ['Tab']),
    operations: [
        new Post(
            uriTemplate: '/tab',
            input: TabPostInput::class,
            name: 'api_tab_post',
        ),
        new GetCollection(
            uriTemplate: '/tabs',
            output: false,
            name: 'api_tab_collection',
        ),
        new Get(
            uriTemplate: '/tab/{id}',
            output: false,
            name: 'api_tab_get',
        ),
        new Put(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_put',
        ),
        new Patch(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_patch',
        ),
        new Delete(
            uriTemplate: '/tab/{id}',
            name: 'api_tab_delete',
        ),
    ]
)]
final class Tab
{

}
