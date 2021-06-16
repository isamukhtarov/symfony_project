<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Validation\Constraint\IfThen;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Ria\Bundle\CoreBundle\Component\CommandValidator\Validatable;

class AddVoteCommand implements Validatable
{
    #[Assert\NotBlank]
    public int|null $voteId = null;

    #[Assert\NotBlank]
    public int|null $optionId = null;

    /**
     * @IfThen("this.recaptchaIsRequired()", constraints={@Recaptcha3})
     */
    public string|null $recaptcha = null;

    #[Pure] public function recaptchaIsRequired(): bool
    {
        return !is_null($this->recaptcha);
    }

}