<?php

namespace App\ApiResource\Content;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\Note\NotePostInput;
use App\Dto\Note\NotePutInput;
use App\Dto\Note\NotePatchInput;
use App\State\Note\NotePostProcessor;
use App\State\Note\NotePutProcessor;
use App\State\Note\NotePatchProcessor;
use App\State\Note\NoteDeleteProcessor;
use App\Entity\Content\Note as NoteEntity;
use App\Entity\Content\Schema as SchemaEntity;
use App\Entity\Page\Tab as TabEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use DateTimeInterface;

#[ApiResource(
    shortName: 'Note',
    openapi: new Operation(tags: ['Note']),
    stateOptions: new Options(entityClass: NoteEntity::class),
    operations: [
        new Post(
            uriTemplate: '/note',
            name: 'api_note_post',
            processor: NotePostProcessor::class,
            input: NotePostInput::class,
        ),
        new GetCollection(
            uriTemplate: '/notes',
            name: 'api_note_collection',
        ),
        new Get(
            uriTemplate: '/note/{id}',
            name: 'api_note_get',
        ),
        new Put(
            uriTemplate: '/note/{id}',
            name: 'api_note_put',
            processor: NotePutProcessor::class,
            input: NotePutInput::class,
            security: "is_granted('write', object)"
        ),
        new Patch(
            uriTemplate: '/note/{id}',
            name: 'api_note_patch',
            processor: NotePatchProcessor::class,
            input: NotePatchInput::class,
            security: "is_granted('write', object)"
        ),
        new Delete(
            uriTemplate: '/note/{id}',
            name: 'api_note_delete',
            read: false,
            output: false,
            processor: NoteDeleteProcessor::class,
        ),
    ]
)]

#[ApiFilter(SearchFilter::class, properties: ['tab' => 'exact'])]
#[Map(source: NoteEntity::class)]
final class Note
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[ApiProperty(readable: false, writable: true)]
    public ?string $iri = null;

    public ?string $name = null;

    public ?string $nameDefault = null;

    public ?int $position = null;

    public ?DateTimeInterface $date = null;

    public ?SchemaEntity $schema = null;

    public ?TabEntity $tab = null;

    public ?string $content = null;
}