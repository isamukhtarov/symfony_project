<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\VoteBundle\Command\UpdateVoteCommand;
use Ria\Bundle\VoteBundle\Entity\Option;
use Ria\Bundle\VoteBundle\Entity\Type;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;

class UpdateVoteHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VoteRepository $votesRepository
    ){}

    /**
     * @param UpdateVoteCommand $command
     * @throws Exception
     */
    public function handle(UpdateVoteCommand $command): void
    {
        $vote = $command->getVote();

        $vote->setTitle($command->title)
             ->setStatus($command->status)
             ->setLanguage($command->locale)
             ->setShowOnMainPage($command->showOnMainPage)
             ->setShowRecaptcha($command->showRecaptcha)
             ->setType(new Type($command->type))
             ->setPhoto(
                $command->photo ?
                      $this->entityManager->getPartialReference(Photo::class, $command->photo) : null
            )
             ->setStartDate(new DateTime($command->startDate) ?: new DateTime())
             ->setUpdatedAt(new DateTime());

        if (!empty($command->endDate)) {
            $vote->setEndDate(new DateTime($command->endDate));
        }

        $options = new ArrayCollection();
        foreach ($command->options as $optionCommand) {
            if (!($optionCommand->id)) {
                $option = new Option();
                $option->setScore(0);
            } else {
                $option = $this->entityManager->find(Option::class, $optionCommand->id);
            }

            $option->setTitle($optionCommand->title)
                   ->setSort($optionCommand->sort);

            $vote->addOption($option);
            $options->add($option);
        }

        $vote->sync('options', $options);
        $this->votesRepository->save($vote);
    }
}