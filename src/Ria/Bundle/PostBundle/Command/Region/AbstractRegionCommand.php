<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractRegionCommand
{
    #[Assert\Type('integer')]
    public int $sort;

    #[Assert\Valid]
    public array $translations = [];
}