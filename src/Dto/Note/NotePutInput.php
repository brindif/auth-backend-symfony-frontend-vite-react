<?php
namespace App\Dto\Note;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabExists;
use App\Validator\Content\SchemaExists;
use ApiPlatform\Metadata\ApiProperty;
use DateTimeInterface;

final class NotePutInput
{
    #[Assert\Length(max: 50, maxMessage: 'note.error.name.length')]
    #[Assert\NotBlank(message: 'note.error.name.empty')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'note.error.name.default.length')]
    public ?string $nameDefault = null;

    #[Assert\PositiveOrZero(message: 'note.error.position.nan')]
    public ?int $position = null;
    
    #[Assert\Type(type: 'date', message: 'note.error.date.type')]
    public ?DateTimeInterface $date = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'example' => '/schema/123',
            'format' => 'iri-reference',
            'pattern' => "^/schema/\\d+$",
            'description' => 'The IRI of the schema note. Must be a valid note IRI or null.',
            'x-list' => [
                'route' => '/schemas',
                'label' => 'name',
                'identifier' => '@id',
                'labelDefault' => 'nameDefault',
            ],
        ],
    )]
    #[Assert\Type(type: 'string', message: 'note.error.schema.type')]
    #[SchemaExists]
    public ?string $schema = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'example' => '/tab/123',
            'format' => 'iri-reference',
            'pattern' => "^/tab/\\d+$",
            'description' => 'The IRI of the note tab.',
            'x-list' => [
                'route' => '/tabs',
                'label' => 'name',
                'identifier' => '@id',
                'labelDefault' => 'nameDefault',
            ],
        ],
    )]
    #[Assert\Type(type: 'string', message: 'note.error.tab.type')]
    #[TabExists]
    public ?string $tab = null;
}
