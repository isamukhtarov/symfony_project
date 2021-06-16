<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Handler;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CurrencyBundle\Entity\Currency;
use Ria\Bundle\CurrencyBundle\Command\CreateCurrencyCommand;
use Ria\Bundle\CurrencyBundle\Repository\CurrencyRepository;
use Ria\Bundle\CurrencyBundle\Resource\Resource;

class CreateCurrencyHandler
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function handle(CreateCurrencyCommand $command): void
    {
        $codes = array_map(function (Resource $resource) {
            return $resource->getCode();
        }, $command->getResources());

        $date = date('Y-m-d');

        $existing = $this->currencyRepository->getExisting($codes, $date);

        $this->saveCurrencies($existing, $command->getResources(), $date);
    }

    private function saveCurrencies(array $existing, array $resources, string $date): void
    {
        foreach ($resources as $resource) {
            if (isset($existing[$resource->getCode()])) {
                $currency = $existing[$resource->getCode()];
            } else {
                $currency = new Currency();
                $currency
                    ->setNominal($resource->getNominal())
                    ->setCode($resource->getCode())
                    ->setDate(new DateTime($date))
                    ->setCreatedAt(new DateTime());
            }

            $currency
                ->setValue($resource->getValue())
                ->setUpdatedAt(new DateTime());

            $previous = $this->currencyRepository->getPrevious($currency);
            $diff     = $previous ? $currency->getValue() - $previous->getValue() : 0;

            $currency->setDifference((float)$diff);

            $this->entityManager->persist($currency);
        }

        $this->entityManager->flush();
    }
}