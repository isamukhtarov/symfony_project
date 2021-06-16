<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\City;

use Ria\Bundle\PostBundle\Entity\City\City;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Entity\City\Translation;
use Ria\Bundle\PostBundle\Command\City\CreateCityCommand;
use Ria\Bundle\PostBundle\Query\Repository\CityRepository;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;

class CreateCityHandler
{
    public function __construct(
        private CityRepository $cityRepository,
        private RegionRepository $regionRepository
    ){}

    public function handle(CreateCityCommand $command): void
    {
        $city = new City();
        /** @var Region $region */
        $region = $this->regionRepository->find($command->regionId);
        $city->setRegion($region);

        foreach ($command->translations as $translationCommand) {
            $translation = new Translation();

            $translation->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug)
                ->setLanguage($translationCommand->locale);

            $city->addTranslation($translation);
        }

        $this->cityRepository->save($city);
    }
}