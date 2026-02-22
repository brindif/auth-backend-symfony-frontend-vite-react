<?php
namespace App\Dto\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabExists;
use ApiPlatform\Metadata\ApiProperty;

final class SchemaPostInput
{
    #[Assert\Length(max: 50, maxMessage: 'schema.error.name.length')]
    #[Assert\NotBlank(message: 'schema.error.name.empty')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'schema.error.name.default.length')]
    public ?string $nameDefault = null;

    #[Assert\PositiveOrZero(message: 'schema.error.position.nan')]
    public ?int $position = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'example' => '/tab/123',
            'format' => 'iri-reference',
            'pattern' => "^/tab/\\d+$",
            'description' => 'The IRI of the parent schema. Must be a valid schema IRI or null.',
            'x-list' => [
                'route' => '/tabs',
                'label' => 'name',
                'identifier' => '@id',
                'labelDefault' => 'nameDefault',
            ],
        ],
    )]
    #[Assert\Type(type: 'string', message: 'schema.error.tab.type')]
    #[TabExists]
    public ?string $tab = null;
}
