<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Validaton\Constraint;

use Ria\Bundle\VoteBundle\Validaton\Validator\PhotoValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Photo extends Constraint
{
    public function validatedBy(): string
    {
        return PhotoValidator::class;
    }
}