<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Command\Person;

use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;
use Ria\Bundle\PersonBundle\Entity\Person\Translation;

#[CommandUniqueEntity([
    'entityClass' => Translation::class,
    'fieldMapping' => ['slug' => 'slug', 'locale' => 'language']
])]
class CreatePersonTranslationCommand extends PersonTranslationCommand
{}