<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\CoreBundle\Validation\Validator\TimestampValidator;

/**
 * @Annotation
 */
#[Attribute] class Timestamp extends Constraint
{
    public string $message = '"{{ field }}" is not valid timestamp.';

    #[Pure] public function validatedBy(): string
    {
        return TimestampValidator::class;
    }
}