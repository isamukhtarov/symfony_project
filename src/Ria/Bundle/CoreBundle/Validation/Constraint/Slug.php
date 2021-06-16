<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use Ria\Bundle\CoreBundle\Validation\Validator\SlugValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]class Slug extends Constraint
{
    public function validatedBy(): string
    {
        return SlugValidator::class;
    }
}