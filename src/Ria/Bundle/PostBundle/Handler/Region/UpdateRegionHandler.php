<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Region;

use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Ria\Bundle\PostBundle\Command\Region\UpdateRegionCommand;
use Ria\Bundle\PostBundle\Entity\Region\Translation as RegionTranslation;

class UpdateRegionHandler
{
    public function __construct(
        private RegionRepository $regionRepository,
    ){}

    public function handle(UpdateRegionCommand $command): void
    {
        $region = $command->getRegion();
        $region->setSort($command->sort);

        foreach ($command->translations as $translationCommand) {
            $translation = $region->getTranslation($translationCommand->locale) ?: new RegionTranslation();
            $translation
                ->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug);

            $region->addTranslation($translation);
        }

        $this->regionRepository->save($region);
    }
}