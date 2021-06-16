<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Command;


class WeatherReceiverCommand
{
    public function __construct(
        private array $weather
    ){}

    public function getWeather(): array
    {
        return $this->weather;
    }
}