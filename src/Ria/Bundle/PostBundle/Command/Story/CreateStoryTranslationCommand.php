<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Story;

use Ria\Bundle\PostBundle\Entity\Story\Translation;
use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;

#[CommandUniqueEntity([
    'entityClass' => Translation::class,
    'fieldMapping' => ['slug' => 'slug', 'locale' => 'language']
])]
class CreateStoryTranslationCommand extends StoryTranslationCommand
{
}