<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\City;

use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;
use Ria\Bundle\PostBundle\Entity\City\Translation;

#[CommandUniqueEntity([
    'entityClass' => Translation::class,
    'fieldMapping' => ['slug' => 'slug', 'locale' => 'language']
])]
class CreateCityTranslationCommand extends CityTranslationCommand
{}