<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\City;

use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Command\City\UpdateCityCommand;
use Ria\Bundle\PostBundle\Query\Repository\CityRepository;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;

class UpdateCityHandler
{
    public function __construct(
        private CityRepository $cityRepository,
        private RegionRepository $regionRepository
    ){}

    public function handle(UpdateCityCommand $command)
    {
        $city = $command->getCity();
        /** @var Region $region */
        $region = $this->regionRepository->find($command->regionId);
        $city->setRegion($region);

        foreach ($command->translations as $translationCommand) {
            $translation = $city->getTranslation($translationCommand->locale);
            $translation->setTitle($translationCommand->title)->setSlug($translationCommand->slug);
            $city->addTranslation($translation);
        }

        $this->cityRepository->save($city);
    }
}