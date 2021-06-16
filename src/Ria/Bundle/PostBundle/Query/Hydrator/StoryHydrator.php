<?php

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\StoryViewModel;

class StoryHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'story_hydrator';

    protected function getViewModelClassName(): string
    {
        return StoryViewModel::class;
    }
}