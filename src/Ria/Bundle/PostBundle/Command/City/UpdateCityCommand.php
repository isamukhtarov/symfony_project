<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\City;

use Ria\Bundle\PostBundle\Entity\City\City;

class UpdateCityCommand extends AbstractCityCommand
{
    private City $city;

    public function __construct(City $city, array $locales, string $currentLocale)
    {
        $this->city = $city;
        $this->regionId = $city->getRegion()->getId();

        foreach ($locales as $locale)
            $this->translations[$locale] = new CityTranslationCommand($locale, $city->getTranslation($locale));
        $this->currentLocale = $currentLocale;
    }

    public function getCity(): City
    {
        return $this->city;
    }
}