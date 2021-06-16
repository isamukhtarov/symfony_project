<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

use Ria\Bundle\VoteBundle\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

class VoteCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Type('boolean')]
    public bool $showRecaptcha;

    #[Assert\Type('boolean')]
    public bool $showOnMainPage;

    #[Assert\NotBlank]
    public ?string $locale;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $type;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $startDate;

    #[Assert\Type('string')]
    public ?string $endDate;

    public array $options;

    public function isPrizeType(): bool
    {
        return $this->type === Type::TYPE_PRIZE;
    }
}