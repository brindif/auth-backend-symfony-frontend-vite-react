<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Auth\UpdateRequestController;

final class UpdateRequestInput
{
    #[Assert\NotBlank(message: 'update.request.error.email.empty')]
    #[Assert\Email(message: 'update.request.error.email.format')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'update.request.error.type.empty')]
    #[Assert\Choice(choices: UpdateRequestController::TYPES, message: 'update.request.error.type.invalid')]
    public ?string $type = null;
}
