<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Region;

use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Ria\Bundle\PostBundle\Command\Region\DeleteRegionCommand;

class DeleteRegionHandler
{
    public function __construct(
        private RegionRepository $regionRepository,
    ){}

    public function handle(DeleteRegionCommand $command): void
    {
        if (($region = $this->regionRepository->find($command->getId())) === null) return;
        $this->regionRepository->remove($region);
    }
}