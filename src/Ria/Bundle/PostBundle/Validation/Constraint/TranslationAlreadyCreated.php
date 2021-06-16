<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Ria\Bundle\PostBundle\Validation\Validator\TranslationValidator;

/**
 * @Annotation
 */
class TranslationAlreadyCreated extends Constraint
{
    public function validatedBy(): string
    {
        return TranslationValidator::class;
    }
}