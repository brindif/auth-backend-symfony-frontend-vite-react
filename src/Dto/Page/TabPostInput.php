<?php
namespace App\Dto\Page;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabExists;
use ApiPlatform\Metadata\ApiProperty;
use App\Enum\MenuEnum;

final class TabPostInput
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
        ],
        readableLink: false,
        writableLink: false
    )]
    #[Assert\Type(type: 'string', message: 'tab.error.parent.type')]
    #[TabExists]
    public ?self $parent = null;

    #[Assert\NotNull(message: 'tab.error.menu.empty')]
    #[Assert\Choice(choices: [MenuEnum::RIGHT, MenuEnum::TOP], message: 'tab.error.menu.invalid')]
    public ?MenuEnum $menu = null;
}
