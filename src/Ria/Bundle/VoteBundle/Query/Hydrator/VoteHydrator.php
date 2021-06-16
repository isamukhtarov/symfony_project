<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\VoteBundle\Query\ViewModel\VoteViewModel;

class VoteHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'vote_hydrator';

    protected function getViewModelClassName(): string
    {
        return VoteViewModel::class;
    }

}