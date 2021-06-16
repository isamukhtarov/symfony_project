<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;


use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;

class RegionDataTransformer
{
    public function __construct(
        private RegionRepository $regionRepository
    ){}

    public function transform(array $params = []): array
    {
        $tree = [];
        $language = $params['language'] ?? 'az';

        foreach ($this->regionRepository->getAll($language) as $region) {
            $tree[$region['title']] = $region['id'];
        }

        return $tree;
    }
}