<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\RegionViewModel;

class RegionHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'region_hydrator';

    protected function getViewModelClassName(): string
    {
        return RegionViewModel::class;
    }
}