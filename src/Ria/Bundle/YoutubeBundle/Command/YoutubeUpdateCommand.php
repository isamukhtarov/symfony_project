<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Command;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;
use Symfony\Component\Validator\Constraints as Assert;

class YoutubeUpdateCommand
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $youtubeId;

    #[Assert\Type('boolean')]
    public bool $status;

    #[Pure]public function __construct(YouTube $youTube)
    {
        $this->id = $youTube->getId();
        $this->youtubeId = $youTube->getYoutubeId();
        $this->status = $youTube->getStatus();
    }
}