<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Hydrator;


use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PersonBundle\Query\ViewModel\PersonViewModel;

/**
 * Class PersonHydrator
 * @package Ria\Bundle\PersonBundle\Query\Hydrator
 */
class PersonHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'person_hydrator';

    public function getViewModelClassName(): string
    {
       return PersonViewModel::class;
    }
}