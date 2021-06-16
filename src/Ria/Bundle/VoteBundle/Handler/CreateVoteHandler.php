<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTime;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\VoteBundle\Command\CreateVoteCommand;
use Ria\Bundle\VoteBundle\Entity\Option;
use Ria\Bundle\VoteBundle\Entity\Type;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;

class CreateVoteHandler
{
    public function __construct(
        private VoteRepository $votesRepository,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param CreateVoteCommand $command
     * @throws Exception
     */
    public function handle(CreateVoteCommand $command): void
    {
        $vote = new Vote;

        $vote->setTitle($command->title)
              ->setLanguage($command->locale)
              ->setStatus($command->status)
              ->setShowOnMainPage($command->showOnMainPage)
              ->setShowRecaptcha($command->showRecaptcha)
              ->setType(new Type($command->type))
              ->setStartDate(new DateTime($command->startDate) ?: new DateTime());

        if ($command->photo) {
            $vote->setPhoto($this->entityManager->getPartialReference(Photo::class, $command->photo));
        }

        if (!empty($command->endDate)) {
            $vote->setEndDate(new DateTime($command->endDate));
        }

        foreach ($command->options as $optionCommand) {
            $option = new Option;

            $option->setTitle($optionCommand->title)
                   ->setSort($optionCommand->sort)
                   ->setScore(0);

            $vote->addOption($option);
        }

        $this->votesRepository->save($vote);
    }
}