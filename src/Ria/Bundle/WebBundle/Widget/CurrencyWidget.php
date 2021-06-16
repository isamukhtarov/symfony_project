<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\CurrencyBundle\Repository\CurrencyRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Twig\Environment;

/**
 * Class CurrencyWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property string type
 * @property array items
 */
class CurrencyWidget extends FrontendWidget
{
    protected array $cacheKeys = [];

    public const TYPE_HEADER    = 'header';
    public const TYPE_LIST      = 'list';
    public const TYPE_CONVERTER = 'converter';

    public function __construct(
        private RequestStack $requestStack,
        protected Environment $twig,
        protected RedisClient $redisClient,
        protected CurrencyRepository $repository,
        protected ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($twig);

        $this->cacheKeys = $parameterBag->get('app.cache_keys');
    }

    public function run(): string
    {
        $items = match ($this->type) {
            self::TYPE_HEADER    => $this->getCurrencyForHeader(),
            self::TYPE_LIST      => $this->getCurrencyList(),
            self::TYPE_CONVERTER => $this->items,
            default              => null
        };

        return $this->render('currency-' . $this->type . '.html.twig', compact('items'));
    }

    protected function getCurrencyForHeader(): array
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if (!$this->redisClient->exists("{$this->cacheKeys['currency']}")) {
            $this->redisClient->set(
                $this->cacheKeys['currency'],
                serialize($this->repository->lastCommonRecords())
            );
        }

        $items = unserialize($this->redisClient->get($this->cacheKeys['currency']));

        array_walk($items, function (&$item) {
            $item['value'] = number_format($item['value'], $item['code'] == 'BRENT' ? 2 : 4);
        });

        return $items;
    }

    protected function getCurrencyList(): array
    {
        $mainCodes = ['USD', 'EUR', 'AZN', 'RUB', 'GEL', 'TRY', 'UAH'];

        $allItems = $this->items;
        $firstItems = [];
        foreach ($allItems as $i => $item) {
            if (in_array($item['code'], $mainCodes)) {
                $firstItems[array_search($item['code'], $mainCodes)] = $item;
                unset($allItems[$i]);
            }
        }
        ksort($firstItems);
        return array_merge($firstItems, $allItems);
    }

}