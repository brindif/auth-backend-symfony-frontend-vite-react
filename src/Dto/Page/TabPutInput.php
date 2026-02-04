<?php
namespace App\Dto\Page;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Page\TabExists;
use App\Validator\Page\PermissionsFormat;
use ApiPlatform\Metadata\ApiProperty;
use App\Enum\PermissionEnum;
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

    #[ApiProperty(
        openapiContext: [
            'type' => 'array',
            'example' => '[{user: "/user/1", permission: "read"}, {user: "/user/2", permission: "manage"}]',
            'format' => 'join-list',
            'description' => 'User permission for this tab.',
            'x-join' => [
                'required' => ['user', 'permission'],
                'properties' => [
                    'user' => [
                        'format' => 'iri-reference',
                        'x-list' => [
                            'route' => '/users',
                            'label' => 'email',
                            'identifier' => '@id',
                            'labelDefault' => 'name',
                        ],
                    ],
                    'permission' => [
                        'enum' => [
                            PermissionEnum::READ->value,
                            PermissionEnum::WRITE->value,
                            PermissionEnum::MANAGE->value
                        ],
                    ],
                ],
            ],
        ],
    )]
    #[PermissionsFormat]
    #[Assert\Type(type: 'array', message: 'tab.error.permissions.type')]
    public ?array $permissions = null;
}
