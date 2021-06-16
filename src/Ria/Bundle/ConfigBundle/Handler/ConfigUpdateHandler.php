<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Handler;

use Ria\Bundle\ConfigBundle\Command\ConfigUpdateCommand;
use Ria\Bundle\ConfigBundle\Repository\ConfigRepository;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class ConfigUpdateHandler
{
    public function __construct(
        private ConfigRepository $configRepository,
        private ClientInterface $redisClient
    ){}

    public function handle(ConfigUpdateCommand $command): void
    {
        $config = $this->configRepository->find($command->id);

        $config
            ->setValue($command->value)
            ->setLabel($command->label);

        $this->configRepository->save($config);

        $this->redisClient->remove('app-config');
    }
}