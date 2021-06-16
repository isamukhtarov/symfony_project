<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;
use Ria\Bundle\PostBundle\Entity\Category\Translation;

#[CommandUniqueEntity([
    'entityClass' => Translation::class,
    'fieldMapping' => ['slug' => 'slug', 'locale' => 'language']
])]
class CreateCategoryTranslationCommand extends CategoryTranslationCommand
{
}