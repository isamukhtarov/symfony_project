<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

/**
 * Class Collector
 * @package Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector
 */
abstract class Collector
{

    private bool $fakeDataPreferred = false;

    public function __construct(
        protected ParameterBagInterface $parameterBag,
        protected ClientInterface $redisClient
    ) {}

    public function useFakeData(bool $useFakeData): void
    {
        $this->fakeDataPreferred = $useFakeData;
    }

    public function isFakeDataPreferred(): bool
    {
        return $this->fakeDataPreferred;
    }

    abstract public function collect(): array;

    abstract public function getFakeData(): array;
}