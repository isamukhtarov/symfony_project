<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\City;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractCityCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $regionId;

    #[Assert\Valid]
    public array $translations;

    protected string $currentLocale;

    public function getCurrentLocale(): string
    {
        return $this->currentLocale;
    }
}