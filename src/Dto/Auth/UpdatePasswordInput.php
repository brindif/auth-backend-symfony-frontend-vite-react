<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class UpdatePasswordInput
{
    #[Assert\NotBlank(message: 'update.password.error.email.empty')]
    #[Assert\Email(message: 'update.password.error.email.format')]
    public ?string $email = null;

    #[ApiProperty(openapiContext: [
        "type" => "string",
        "minLength" => 6,
        "example" => "Password-with-at-least-6-char!"
    ])]
    #[Assert\NotBlank(message: 'update.password.error.password.empty')]
    #[Assert\Length(min: 6, minMessage: 'update.password.error.password.short')]
    public ?string $password = null;
}
