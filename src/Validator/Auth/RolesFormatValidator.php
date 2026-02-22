<?php
namespace App\Validator\Auth;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Enum\RoleEnum;

class RolesFormatValidator extends ConstraintValidator
{
    public function __construct(private IriConverterInterface $iriConverter) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RolesFormat) {
            throw new UnexpectedTypeException($constraint, RolesFormat::class);
        }

        if (empty($value) || !is_array($value)) {
            throw new UnexpectedValueException($constraint, RolesFormat::class);
        }

        foreach ($value as $role) {
            if (!is_string($role)) {
                $this->context->buildViolation($constraint->message)->addViolation();
                return;
            }
            if (!in_array($role, [
                RoleEnum::ADMIN->value,
                RoleEnum::MANAGER->value,
                RoleEnum::USER->value,
                RoleEnum::GUEST->value,
            ])) {
                $this->context->buildViolation($constraint->message)->addViolation();
                return;
            }
        }
    }
}
