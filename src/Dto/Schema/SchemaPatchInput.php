<?php
namespace App\Dto\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class SchemaPatchInput
{
  #[ApiProperty(
    openapiContext: [
      'type' => 'string',
      'format' => 'schema',
    ],
  )]
  #[Assert\Type(type: 'string', message: 'schema.error.content.type')]
  public ?string $content = null;
}
