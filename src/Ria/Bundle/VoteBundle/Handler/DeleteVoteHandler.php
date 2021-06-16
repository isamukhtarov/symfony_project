<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Handler;

use Ria\Bundle\VoteBundle\Command\DeleteVoteCommand;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;

class DeleteVoteHandler
{
    public function __construct(
        private VoteRepository $votesRepository
    ){}

    public function handle(DeleteVoteCommand $command): void
    {
        if (($vote = $this->votesRepository->find($command->getId())) === null) return;
        $this->votesRepository->remove($vote);
    }
}