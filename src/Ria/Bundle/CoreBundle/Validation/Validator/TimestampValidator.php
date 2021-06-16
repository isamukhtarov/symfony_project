<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\CoreBundle\Validation\Constraint\Timestamp;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TimestampValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!($constraint instanceof Timestamp))
            throw new UnexpectedTypeException($constraint, Timestamp::class);

        if (!preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $value))
            $this->context->buildViolation($constraint->message)->setParameter('{{ field }}', $value)->addViolation();
    }
}