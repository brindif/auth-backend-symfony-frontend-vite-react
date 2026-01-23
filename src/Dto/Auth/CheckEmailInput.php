<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class CheckEmailInput
{
    #[Assert\NotBlank(message: 'check.email.error.token.empty')]
    public ?string $token = null;
}
