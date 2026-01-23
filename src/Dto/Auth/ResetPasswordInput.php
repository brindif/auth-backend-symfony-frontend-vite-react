<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class ResetPasswordInput
{
    #[Assert\NotBlank(message: 'reset.password.error.email.empty')]
    #[Assert\Email(message: 'reset.password.error.email.format')]
    public ?string $email = null;

    #[ApiProperty(openapiContext: [
        "type" => "string",
        "minLength" => 6,
        "example" => "Password-with-at-least-6-char!"
    ])]
    #[Assert\NotBlank(message: 'reset.password.error.password.empty')]
    #[Assert\Length(min: 6, minMessage: 'reset.password.error.password.short')]
    public ?string $password = null;
}
