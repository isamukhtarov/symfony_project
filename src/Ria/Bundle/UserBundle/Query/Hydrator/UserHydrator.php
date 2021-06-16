<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Query\Hydrator;

use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\UserBundle\Query\ViewModel\UserViewModel;

class UserHydrator extends ViewModelHydrator
{
    public const HYDRATION_MODE = 'user_hydrator';

    protected function getViewModelClassName(): string
    {
        return UserViewModel::class;
    }
}