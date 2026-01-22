<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class RegisterInput
{
    #[Assert\NotBlank(message: 'register.error.email.empty')]
    #[Assert\Email(message: 'register.error.email.format')]
    public ?string $email = null;

    #[ApiProperty(openapiContext: [
        "type" => "string",
        "minLength" => 6,
        "example" => "Password-with-at-least-6-char!"
    ])]
    #[Assert\NotBlank(message: 'register.error.password.empty')]
    #[Assert\Length(min: 6, minMessage: 'register.error.password.short')]
    public ?string $password = null;
}
