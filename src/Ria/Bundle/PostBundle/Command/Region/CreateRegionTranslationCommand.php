<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

use Ria\Bundle\PostBundle\Entity\Region\Translation;
use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;

#[CommandUniqueEntity([
    'entityClass' => Translation::class,
    'fieldMapping' => ['slug' => 'slug', 'locale' => 'language']
])]
class CreateRegionTranslationCommand extends RegionTranslationCommand
{}