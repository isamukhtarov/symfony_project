<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\CoreBundle\Validation\Validator\IfThenValidator;

/**
 * @Annotation
 */
#[Attribute] class IfThen extends Constraint
{
    public string $condition;
    public array $constraints = [];

    public function getDefaultOption(): string
    {
        return 'condition';
    }

    public function getRequiredOptions(): array
    {
        return ['condition'];
    }

    public function validatedBy(): string
    {
        return IfThenValidator::class;
    }
}