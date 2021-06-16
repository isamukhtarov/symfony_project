<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\CoreBundle\Validation\Validator\EntityExistsValidator;

/**
 * @Annotation
 */
#[Attribute] class EntityExists extends Constraint
{
    public string $message = 'Entity "%entity%" with property "%property%": "%value%" does not exist.';
    public string $property = 'id';
    public string $entity;

    public function getDefaultOption(): string
    {
        return 'entity';
    }

    public function getRequiredOptions(): array
    {
        return ['entity'];
    }

    public function validatedBy(): string
    {
        return EntityExistsValidator::class;
    }
}