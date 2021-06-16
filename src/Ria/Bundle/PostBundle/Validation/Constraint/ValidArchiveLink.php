<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Validation\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\PostBundle\Validation\Validator\ArchiveLinkValidator;

/**
 * @Annotation
 */
#[Attribute] class ValidArchiveLink extends Constraint
{
    public function validatedBy(): string
    {
        return ArchiveLinkValidator::class;
    }
}