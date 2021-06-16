<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Region;

use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Command\Region\CreateRegionCommand;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Ria\Bundle\PostBundle\Entity\Region\Translation as RegionTranslation;

class CreateRegionHandler
{
    public function __construct(
        private RegionRepository $regionRepository,
    ){}

    public function handle(CreateRegionCommand $command): void
    {
        $region = new Region();
        $region->setSort($command->sort);

        foreach ($command->translations as $translationCommand) {
            $region->addTranslation((new RegionTranslation())
                ->setLanguage($translationCommand->locale)
                ->setSlug($translationCommand->slug)
                ->setTitle($translationCommand->title));
        }

        $this->regionRepository->save($region);
    }
}