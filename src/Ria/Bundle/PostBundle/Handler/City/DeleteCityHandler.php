<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\City;

use Ria\Bundle\PostBundle\Command\City\DeleteCityCommand;
use Ria\Bundle\PostBundle\Query\Repository\CityRepository;

class DeleteCityHandler
{
    public function __construct(
        private CityRepository $cityRepository
    ){}

    public function handle(DeleteCityCommand $command)
    {
        if (($city = $this->cityRepository->find($command->id)) === null) return;
        $this->cityRepository->remove($city);
    }
}