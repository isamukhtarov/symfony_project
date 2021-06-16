<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\CoreBundle\Validation\Validator\LinksValidator;

/**
 * @Annotation
 */
#[Attribute] class ValidLinks extends Constraint
{
    public function validatedBy(): string
    {
        return LinksValidator::class;
    }
}