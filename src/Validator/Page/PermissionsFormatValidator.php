<?php
namespace App\Validator\Page;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Enum\PermissionEnum;

class PermissionsFormatValidator extends ConstraintValidator
{
    public function __construct(private IriConverterInterface $iriConverter) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PermissionsFormat) {
            throw new UnexpectedTypeException($constraint, PermissionsFormat::class);
        }

        if (empty($value)) {
            return;
        }
        
        if (!is_array($value)) {
            return;
        }

        try {
            foreach ($value as $permission) {
                if (!is_string($permission['user']) || !is_string($permission['permission'])) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                    return;
                }
                if (!in_array($permission['permission'], [
                    PermissionEnum::READ->value,
                    PermissionEnum::WRITE->value,
                    PermissionEnum::MANAGE->value,
                ])) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                    return;
                }
                $this->iriConverter->getResourceFromIri($permission);
            }
        } catch (\Throwable $e) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
