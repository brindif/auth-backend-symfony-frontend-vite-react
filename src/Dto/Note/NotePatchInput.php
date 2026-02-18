<?php
namespace App\Dto\Note;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class NotePatchInput
{
  #[ApiProperty(
    openapiContext: [
      'type' => 'string',
      'format' => 'editor',
    ],
  )]
  #[Assert\Type(type: 'string', message: 'note.error.content.type')]
  public ?string $content = null;
}
