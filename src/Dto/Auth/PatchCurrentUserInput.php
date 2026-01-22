<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final class PatchCurrentUserInput
{
    #[Assert\NotBlank(message: 'register.error.email.empty')]
    public ?string $name = null;
}
