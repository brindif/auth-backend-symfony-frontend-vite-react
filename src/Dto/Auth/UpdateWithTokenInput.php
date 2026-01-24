<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class UpdateWithTokenInput
{
    #[Assert\NotBlank(message: 'update.with.token.error.token.empty')]
    public ?string $token = null;

    #[Assert\Email(message: 'update.with.token.error.email.format')]
    public ?string $email = null;

    #[ApiProperty(openapiContext: [
        "type" => "string",
        "minLength" => 6,
        "example" => "Password-with-at-least-6-char!",
        "nullable" => true
    ])]
    #[Assert\Length(min: 6, minMessage: 'update.with.token.error.password.short')]
    public ?string $password = null;
}
