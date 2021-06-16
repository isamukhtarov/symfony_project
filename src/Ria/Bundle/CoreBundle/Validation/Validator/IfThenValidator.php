<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\CoreBundle\Validation\Constraint\IfThen;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IfThenValidator extends ConstraintValidator
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof IfThen))
            throw new UnexpectedTypeException($constraint, IfThen::class);

        $condition = $this->expressionLanguage->evaluate($constraint->condition, [
            'value' => $value,
            'this'  => $this->context->getObject(),
        ]);

        if ($condition)
            $this->context->getValidator()
                ->inContext($this->context)
                ->validate($value, $constraint->constraints);
    }
}