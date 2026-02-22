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
use App\Dto\Schema\SchemaPostInput;
use App\Dto\Schema\SchemaPutInput;
use App\Dto\Schema\SchemaPatchInput;
use App\State\Schema\SchemaPostProcessor;
use App\State\Schema\SchemaPutProcessor;
use App\State\Schema\SchemaPatchProcessor;
use App\State\Schema\SchemaDeleteProcessor;
use App\State\Schema\SchemaProvider;
use App\Entity\Content\Schema as SchemaEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\IriConverterInterface;

#[ApiResource(
  shortName: 'Schema',
  openapi: new Operation(tags: ['Schema']),
  stateOptions: new Options(entityClass: SchemaEntity::class),
  provider: SchemaProvider::class,
  operations: [
    new Post(
      uriTemplate: '/schema',
      name: 'api_schema_post',
      processor: SchemaPostProcessor::class,
      input: SchemaPostInput::class,
    ),
    new GetCollection(
      uriTemplate: '/schemas',
      name: 'api_schema_collection',
    ),
    new Get(
      uriTemplate: '/schema/{id}',
      name: 'api_schema_get',
    ),
    new Patch(
      uriTemplate: '/schema/{id}',
      name: 'api_schema_patch',
      processor: SchemaPatchProcessor::class,
      input: SchemaPatchInput::class,
      security: "is_granted('ROLE_USER', object)"
    ),
    new Put(
      uriTemplate: '/schema/{id}',
      name: 'api_schema_put',
      processor: SchemaPutProcessor::class,
      input: SchemaPutInput::class,
      security: "is_granted('ROLE_USER', object)"
    ),
    new Delete(
      uriTemplate: '/schema/{id}',
      name: 'api_schema_delete',
      read: false,
      output: false,
      processor: SchemaDeleteProcessor::class,
    ),
  ]
)]

#[ApiFilter(SearchFilter::class, properties: ['tabs' => 'exact'])]
#[Map(source: SchemaEntity::class)]
final class Schema
{
  #[ApiProperty(identifier: true)]
  public ?int $id = null;

  #[ApiProperty(readable: false, writable: true)]
  public ?string $iri = null;

  public ?string $name = null;

  public ?string $nameDefault = null;

  public ?iterable $tabs = null;

  public ?string $content = null;
}