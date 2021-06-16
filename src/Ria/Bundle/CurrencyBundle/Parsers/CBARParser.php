<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Parsers;

use Generator;
use Ria\Bundle\CurrencyBundle\Resource\Resource;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CBARParser implements ParserInterface
{
    const SOURCE_URL = 'https://cbar.az/currencies/%s.xml';

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
        $response = $this->client->request('GET', sprintf(self::SOURCE_URL, date('d.m.Y')));

        $xml = simplexml_load_string($response->getContent());

        foreach ($xml->ValType as $valTYpe) {
            foreach ($valTYpe as $currency) {
                $code = strtoupper((string)$currency['Code']);

                if (in_array($code, ['XPD', 'XPT', 'XAG', 'XAU'])) {
                    continue;
                }

                yield new Resource(
                    $code,
                    (float)$currency->Value,
                    (int)$currency->Nominal
                );
            }
        }
    }
}