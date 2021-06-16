<?php
declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Service;

use Ria\Bundle\ConfigBundle\Entity\Config;
use Ria\Bundle\ConfigBundle\Repository\ConfigRepository;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class ConfigPackage
{

    private array $items = [];

    public function __construct(
        protected ConfigRepository $repository,
        protected ClientInterface $redisClient
    )
    {
        $this->loadItems();
    }

    public function get(string $name): mixed
    {
        return $this->items[$name] ?? null;
    }

    protected function loadItems(): void
    {
        $items = unserialize($this->redisClient->get('app-config') ?? '');
        if ($items === false) {
            $items = [];
            /** @var Config[] $records */
            $records = $this->repository->findAll();
            foreach ($records as $record) {
                $items[$record->getParam()] = $record->hasValue() ? $record->getValue() : $record->getDefault();
            }
            $this->redisClient->set('app-config', serialize($items));
        }
        $this->items = $items;
    }

}