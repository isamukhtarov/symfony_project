<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PhotoBundle\Query\ViewModel\PhotoViewModel;

/**
 * Class PhotoHydrator
 * @package Ria\Bundle\PhotoBundle\Query\Hydrator
 */
class PhotoHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'photo_hydrator';

    /**
     * @return string
     */
    protected function getViewModelClassName(): string
    {
        return PhotoViewModel::class;
    }
}