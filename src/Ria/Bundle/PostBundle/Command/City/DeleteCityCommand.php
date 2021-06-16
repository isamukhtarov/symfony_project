<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\City;

/**
 * Class DeleteCityCommand
 * @package Ria\Bundle\PostBundle\Command\City
 */
class DeleteCityCommand
{
    public function __construct(
        public int $id
    ){}
}