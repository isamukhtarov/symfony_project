<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Components;

use DmitryIvanov\DarkSkyApi\DarkSkyApi;
use DmitryIvanov\DarkSkyApi\Weather\DataPoint;
use League\Tactician\CommandBus;
use Ria\Bundle\WeatherBundle\Command\WeatherReceiverCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class WeatherReceiver implements WeatherReceiverInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private CommandBus $bus,
        private TagAwareCacheInterface $cachePool
    ){}

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function receive(): void
    {
        $regions = new RegionList($this->parameterBag->get('regions'));
        $weatherByRegions = [];

        foreach ($regions->all() as $region => $location) {
            try {
                $weatherByRegions[$region] = $this->getWeatherByLocation($location['lat'], $location['long']);
            }catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        $this->bus->handle(new WeatherReceiverCommand($weatherByRegions));
        $this->cachePool->invalidateTags(['weather']);
    }

    /**
     * @param float $lat
     * @param float $long
     * @throws \Throwable
     */
    public function getWeatherByLocation(float $lat, float $long): array
    {
        $weatherByLanguage = [];

        foreach ($this->parameterBag->get('app.supported_locales') as $lang) {
            if ($lang == 'ge') {
                $lang = 'ka';
            }
            $forecast = (new DarkSkyApi($this->getApiKey()))
                ->location($lat, $long)
                ->units('auto')
                ->language($lang)
                ->forecast('daily');

            $weather = $forecast->daily();

            if (empty($weather)) {
                throw new \Exception('Failed to get weather');
            }

            $weatherByLanguage[$lang] = $weather->data();
        }

        return $this->getWeatherData($weatherByLanguage);
    }

    public function getWeatherData(array $weatherByLang): array
    {
        $data = [];
        foreach ($weatherByLang as $lang => $weatherData) {
            /**
             * @var DataPoint $dataPoint
             */
            foreach ($weatherData as $dataPoint) {
                $date = date('Y-m-d', $dataPoint->time());
                if (!isset($data[$date])) {
                    $data[$date] = [
                        'date' => $date,
                        'icon' => $dataPoint->icon(),
                        'temperature' => [
                            'high' => $dataPoint->temperatureHigh(),
                            'low' => $dataPoint->temperatureLow()
                        ],
                        'windSpeed' => $dataPoint->windSpeed(),
                        'humidity' => $dataPoint->humidity(),
                        'summary' => [
                            $lang => $dataPoint->summary()
                        ]
                    ];
                } else {
                    $data[$date]['summary'][$lang] = $dataPoint->summary();
                }
            }
        }

        return $data;
    }

    public function getApiKey(): string
    {
        return $this->parameterBag->get('darkSkyApiKey');
    }
}