<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final class VerifyEmailInput
{
    #[Assert\NotBlank(message: 'verify.email.error.expires.empty')]
    public ?string $expires = null;

    #[Assert\NotBlank(message: 'verify.email.error.signature.empty')]
    public ?string $signature = null;

    #[Assert\NotBlank(message: 'verify.email.error.token.empty')]
    public ?string $token = null;

    #[Assert\NotBlank(message: 'verify.email.error.id.empty')]
    #[Assert\Type(type: 'numeric', message: 'verify.email.error.id.not.numeric')]
    public ?string $id = null;
}
