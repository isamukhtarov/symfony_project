<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

use Ria\Bundle\VoteBundle\Dto\VoteDto;
use Ria\Bundle\VoteBundle\Validaton\Constraint\Photo as PhotoIsRequired;
use Ria\Bundle\CoreBundle\Validation\Constraint\IfThen;

class CreateVoteCommand extends VoteCommand
{

    /**
     * @IfThen("this.isPrizeType()", constraints={@PhotoIsRequired})
     */
    public int|null $photo = null;

    public function __construct(VoteDto $dto)
    {
        $this->locale = $dto->getLocale();

        if (!empty($dto->getRequestOptions())) {
            foreach ($dto->getRequestOptions() as $sort => $option) {
                $this->options[$sort] = new VoteOptionsCommand($option['title'], $sort);
            }
        }
    }
}