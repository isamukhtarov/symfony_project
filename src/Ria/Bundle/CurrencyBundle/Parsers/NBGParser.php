<?php
declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Parsers;

use Generator;
use Ria\Bundle\CurrencyBundle\Resource\Resource;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NBGParser implements ParserInterface
{
    const SOURCE_URL = 'https://www.nbg.gov.ge/index.php?m=582&lng=eng';

    public function __construct(
        private HttpClientInterface $client
    ){}

    /**
     * @return Generator
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function parse(): Generator|array
    {
        $response = $this->client->request('GET', self::SOURCE_URL, ['verify_host' => false]);

        $crawler = new Crawler((string)$response->getContent());
        $currencyItems = $crawler->filter('#currency_id tr');

        $items = $currencyItems->each(function (Crawler $node) {
            $itemElement = $node->filter('td');

            $currencyCode = trim($itemElement->eq(0)->text());
            $value = (float)trim($itemElement->eq(2)->text());

            if (in_array($currencyCode, ['AMD', 'BGN', 'QAR', 'RON', 'ISK', 'HUF', 'RSD'])) {
                return null;
            }

            preg_match('/^([0-9]+)\s.*$/i', trim($itemElement->eq(1)->text()), $matches);
            $nominal = (int)$matches[1];

            return new Resource(
                $currencyCode,
                $value,
                $nominal
            );
        });

        return array_filter($items);
    }
}