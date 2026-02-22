<?php
namespace App\Validator\Content;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use ApiPlatform\Metadata\IriConverterInterface;

class SchemaExistsValidator extends ConstraintValidator
{
    public function __construct(private IriConverterInterface $iriConverter) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SchemaExists) {
            throw new UnexpectedTypeException($constraint, SchemaExists::class);
        }

        if (empty($value)) {
            return;
        }
        
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $this->iriConverter->getResourceFromIri($value);
        } catch (\Throwable $e) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
