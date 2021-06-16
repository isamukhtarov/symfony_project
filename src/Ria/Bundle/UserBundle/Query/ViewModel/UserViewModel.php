<?php
declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Query\ViewModel;

use Ria\Bundle\CoreBundle\Query\ViewModel;

/**
 * Class UserViewModel
 * @package Ria\Bundle\UserBundle\Query\ViewModel
 *
 * @property-read int id
 * @property-read string firstName
 * @property-read string lastName
 */
class UserViewModel extends ViewModel
{
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}