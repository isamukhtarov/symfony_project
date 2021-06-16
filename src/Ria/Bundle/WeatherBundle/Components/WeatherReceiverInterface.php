<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Components;

interface WeatherReceiverInterface
{
    public function receive(): void;
}