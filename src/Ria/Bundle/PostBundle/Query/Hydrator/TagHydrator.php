<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\TagViewModel;

/**
 * Class TagHydrator
 * @package Ria\Bundle\PostBundle\Query\Hydrator
 */
class TagHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'tag_hydrator';

    protected function getViewModelClassName(): string
    {
        return TagViewModel::class;
    }

}
