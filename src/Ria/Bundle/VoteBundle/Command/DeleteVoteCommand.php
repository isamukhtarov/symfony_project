<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

class DeleteVoteCommand
{
    public function __construct(
        private int $id
    ){}

    public function getId(): int
    {
        return $this->id;
    }
}