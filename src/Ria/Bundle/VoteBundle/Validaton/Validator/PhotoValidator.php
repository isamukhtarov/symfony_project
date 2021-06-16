<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Validaton\Validator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhotoValidator extends ConstraintValidator
{
    public function __construct(
        private TranslatorInterface $translator
    ){}
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            $this->context->addViolation($this->translator->trans('form.errors.photo_required'));
    }
}