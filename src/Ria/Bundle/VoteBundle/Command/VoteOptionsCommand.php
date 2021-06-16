<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class VoteOptionsCommand
{
    public int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\Type('int')]
    public int $sort;

    public function __construct(string $title, int $sort)
    {
        $this->title = $title;
        $this->sort = $sort;
    }
}