<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

use Ria\Bundle\PostBundle\Entity\Region\Region;

class UpdateRegionCommand extends AbstractRegionCommand
{
    private Region $region;

    public function __construct(Region $region, array $locales)
    {
        $this->region = $region;
        $this->sort = $region->getSort();

        foreach ($locales as $locale)
            $this->translations[$locale] = new RegionTranslationCommand($locale, $region->getTranslation($locale));
    }

    public function getRegion(): Region
    {
        return $this->region;
    }
}