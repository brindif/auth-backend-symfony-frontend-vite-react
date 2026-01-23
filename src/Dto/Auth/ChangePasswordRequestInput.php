<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

final class ChangePasswordRequestInput
{
    #[Assert\NotBlank(message: 'change.password.request.error.email.empty')]
    #[Assert\Email(message: 'change.password.request.error.email.format')]
    public ?string $email = null;
}
