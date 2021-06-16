<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Ria\Bundle\VoteBundle\Validaton\Constraint\Photo as PhotoIsRequired;
use Ria\Bundle\CoreBundle\Validation\Constraint\IfThen;

class UpdateVoteCommand extends VoteCommand
{
    public Vote $vote;

    /**
     * @IfThen("this.isPrizeType()", constraints={@PhotoIsRequired})
     */
    public Photo|int|null $photo;

    public function __construct(Vote $vote, array $options = [])
    {
        $this->vote = $vote;
        $this->title = $vote->getTitle();
        $this->status = $vote->getStatus();
        $this->showRecaptcha = $vote->showRecaptcha();
        $this->showOnMainPage = $vote->showOnMainPage();
        $this->locale = $vote->getLanguage();
        $this->type = $vote->getType()->toValue();
        $this->photo = $vote->getPhoto();
        $this->startDate = $vote->getStartDate()->format('Y-m-d');
        $this->endDate = !empty($vote->getEndDate()) ? $vote->getEndDate()->format('Y-m-d') : null;

        if (!empty($options)) {
            $sort = 0;
            foreach ($options as $optionKey => $option) {
                $this->options[$optionKey] = new VoteOptionsCommand($option['title'], $sort);
                $sort++;
            }
        }
    }

    public function getVote(): Vote
    {
        return $this->vote;
    }
}