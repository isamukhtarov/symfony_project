<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Parsers;

use Generator;
use Ria\Bundle\CurrencyBundle\Resource\Resource;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrentParser implements BrentParserInterface
{
    const SOURCE_URL = 'https://www.investing.com/currencies/xbr-usd';

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
    public function parse(): Generator
    {
        $response = $this->client->request('GET', self::SOURCE_URL, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36'
            ]
        ]);

        $crawler = new Crawler((string) $response->getContent());

        yield new Resource('BRENT', (float)$crawler->filter('#last_last')->text(), 1);
    }
}