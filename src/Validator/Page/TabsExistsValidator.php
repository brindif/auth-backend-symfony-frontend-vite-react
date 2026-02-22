<?php
namespace App\Validator\Page;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use ApiPlatform\Metadata\IriConverterInterface;

class TabsExistsValidator extends ConstraintValidator
{
  public function __construct(private IriConverterInterface $iriConverter) {}

  public function validate(mixed $value, Constraint $constraint): void
  {
    if (!$constraint instanceof TabsExists) {
      throw new UnexpectedTypeException($constraint, TabsExists::class);
    }

    if (empty($value)) {
      return;
    }
    
    if (!is_array($value)) {
      throw new UnexpectedValueException($value, 'array');
    }

    foreach ($value as $tab){
      try {
        $this->iriConverter->getResourceFromIri($tab);
      } catch (\Throwable $e) {
        $this->context
          ->buildViolation($constraint->message)
          ->setParameter('{{ value }}', $tab)
          ->addViolation();
      }
    }
  }
}
