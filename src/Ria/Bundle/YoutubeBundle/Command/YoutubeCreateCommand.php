<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class YoutubeCreateCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $youtubeId;

    #[Assert\Type('boolean')]
    public bool $status;
}