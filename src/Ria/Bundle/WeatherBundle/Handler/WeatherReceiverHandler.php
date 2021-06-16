<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Handler;

use Ria\Bundle\WeatherBundle\Command\WeatherReceiverCommand;
use Ria\Bundle\WeatherBundle\Entity\Weather;
use Ria\Bundle\WeatherBundle\Repository\WeatherRepository;

class WeatherReceiverHandler
{

    public function __construct(
        private WeatherRepository $weatherRepository
    ){}

    public function handle(WeatherReceiverCommand $command): void
    {
        $model = $this->weatherRepository->findOneByDate(date('Y-m-d'));
        if (empty($model)) {
            $model = new Weather();
            $model->setCreatedAt(new \DateTime());
        }
        $model->setData(json_encode($command->getWeather()));
        $this->weatherRepository->save($model);
    }
}