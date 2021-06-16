<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\WeatherBundle\Repository\WeatherRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Twig\Environment;

/**
 * Class WeatherWidget
 * @package Ria\Bundle\WebBundle\Widget
 */
class WeatherWidget extends FrontendWidget
{
    protected array $cacheKeys = [];

    public function __construct(
        private RequestStack $requestStack,
        protected Environment $twig,
        protected RedisClient $redisClient,
        protected WeatherRepository $weatherRepository,
        protected ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($twig);

        $this->cacheKeys = $parameterBag->get('app.cache_keys');
    }

    public function run(): string
    {
        return $this->render($this->template, ['weather' => $this->getWeather()]);
    }

    protected function getWeather(): ?array
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if (!$this->redisClient->exists("{$this->cacheKeys['weather']}-{$language}")) {
            $this->redisClient->set(
                "{$this->cacheKeys['weather']}-{$language}",
                serialize($this->weatherRepository->getLastOne())
            );
        }

        if (!($record = unserialize($this->redisClient->get("{$this->cacheKeys['weather']}-{$language}"))))
            return null;

        $record['data'] = json_decode($record['data'], true);
        $weather = $record['data']['tbilisi'][date('Y-m-d')] ?? null;

        if (!$weather)
            return null;

        return [
            'temperature' => round($weather['temperature']['high']),
            'windSpeed'   => round($weather['windSpeed']),
        ];
    }

}