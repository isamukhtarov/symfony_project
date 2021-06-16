<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\CoreBundle\Validation\Validator\UniqueColumnValidator;

/**
 * @Annotation
 */
#[\Attribute] class UniqueColumn extends Constraint
{
    public string $message = '"{{ field }}" already exists in our database.';

    public string $entity;
    public string $column;

    #[Pure] public function validatedBy(): string
    {
        return UniqueColumnValidator::class;
    }

    public function getRequiredOptions(): array
    {
        return ['entity', 'column'];
    }
}