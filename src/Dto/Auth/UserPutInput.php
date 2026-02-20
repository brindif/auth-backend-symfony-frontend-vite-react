<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;
use App\Enum\RoleEnum;
use App\Validator\Auth\RolesFormat;

final class UserPutInput
{
    #[Assert\Length(max: 50, maxMessage: 'user.error.name.length')]
    public ?string $name = null;
    
    #[Assert\Length(max: 50, maxMessage: 'user.error.email.length')]
    #[Assert\NotBlank(message: 'user.error.email.empty')]
    public ?string $email = null;

    #[ApiProperty(
        openapiContext: [
            'type' => 'array',
            'example' => '["ROLE_USER", "ROLE_ADMIN"]',
            'format' => 'array',
            'description' => 'List of user roles.',
            'x-array' => [
                'type' => 'string',
                'enum' => [
                    RoleEnum::ADMIN->value,
                    RoleEnum::MANAGER->value,
                    RoleEnum::USER->value
                ],
            ],
        ],
    )]
    #[RolesFormat]
    #[Assert\Type(type: 'array', message: 'user.error.roles.type')]
    public ?array $roles = null;

    #[Assert\Type(type: 'boolean', message: 'user.error.isVerified.type')]
    public bool $isVerified = false;
}
