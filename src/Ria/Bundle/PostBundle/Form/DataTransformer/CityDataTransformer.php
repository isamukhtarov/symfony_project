<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use Ria\Bundle\PostBundle\Query\Repository\CityRepository;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CityDataTransformer
{
    public function __construct(
      private CityRepository $cityRepository,
      private RegionRepository $regionRepository,
      private ParameterBagInterface $parameterBag,
    ){}

    public function transform(array $params = []): array
    {
        $tree = [];
        $language = $params['language'] ?? $this->parameterBag->get('app.locale');

        foreach ($this->regionRepository->getAll($language) as $region)
            foreach ($this->cityRepository->getByRegion((int) $region['id'], $language) as $city)
                $tree[$region['title']][$city['title']] = $city['id'];
        return $tree;
    }
}