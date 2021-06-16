<?php

declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

use Ria\Bundle\PhotoBundle\Entity\Photo;

class PhotoCommand
{
    public function __construct(
        public array $photos = [],
        public Photo|int|null $main = null
    ){}

    public function hasMain(): bool
    {
        return $this->main !== null;
    }

    public function getMain(): Photo|int|null
    {
        return $this->main;
    }
}