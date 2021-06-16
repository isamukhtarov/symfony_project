<?php

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;

class WidgetHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'widget_hydrator';

    /**
     * @return string
     */
    protected function getViewModelClassName(): string
    {
        return WidgetViewModel::class;
    }
}