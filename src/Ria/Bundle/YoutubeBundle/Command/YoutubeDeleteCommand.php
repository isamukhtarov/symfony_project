<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Command;

class YoutubeDeleteCommand
{
    public function __construct(
        private int $id
    ){}

    public function getId(): int
    {
        return $this->id;
    }
}