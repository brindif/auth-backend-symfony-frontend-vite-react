<?php
namespace App\Dto\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabsExists;
use ApiPlatform\Metadata\ApiProperty;

final class SchemaPutInput
{
    #[Assert\Length(max: 50, maxMessage: 'schema.error.name.length')]
    #[Assert\NotBlank(message: 'schema.error.name.empty')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'schema.error.name.default.length')]
    public ?string $nameDefault = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'array',
            'example' => '["/tab/123", "/tab/321"]',
            'description' => 'IRI of tabs.',
            'x-array' => [
                'type' => 'string',
                'format' => 'iri-reference',
                'pattern' => "^/tab/\\d+$",
                'x-list' => [
                    'route' => '/tabs',
                    'label' => 'name',
                    'identifier' => '@id',
                    'labelDefault' => 'nameDefault',
                ],
            ]
        ],
    )]
    #[Assert\Type(type: 'array', message: 'schema.error.tab.type')]
    #[TabsExists]
    public ?array $tabs = null;
}
