<?php
namespace App\Dto\Page;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabExists;
use ApiPlatform\Metadata\ApiProperty;
use App\Enum\TabTypeEnum;

final class TabPutInput
{
    #[Assert\Length(max: 50, maxMessage: 'tab.error.name.length')]
    #[Assert\NotBlank(message: 'tab.error.name.empty')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'tab.error.name.default.length')]
    public ?string $nameDefault = null;

    #[Assert\NotBlank(message: 'tab.error.route.empty')]
    #[Assert\Length(max: 50, maxMessage: 'tab.error.route.length')]
    public ?string $route = null;

    #[Assert\PositiveOrZero(message: 'tab.error.position.nan')]
    public ?int $position = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'example' => '/tab/123',
            'format' => 'iri-reference',
            'pattern' => "^/tab/\\d+$",
            'description' => 'The IRI of the parent tab. Must be a valid tab IRI or null.',
            'x-list' => [
                'route' => '/tabs',
                'label' => 'name',
                'identifier' => '@id',
                'labelDefault' => 'nameDefault',
            ],
        ],
    )]
    #[Assert\Type(type: 'string', message: 'tab.error.parent.type')]
    #[TabExists]
    public ?string $parent = null;

    #[Assert\Choice(choices: [
        TabTypeEnum::NOTES,
        TabTypeEnum::CALENDAR,
        TabTypeEnum::TREE
    ], message: 'tab.error.type.invalid')]
    public ?TabTypeEnum $type = null;
}
