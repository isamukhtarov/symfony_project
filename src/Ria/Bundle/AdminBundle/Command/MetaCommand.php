<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Command;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Symfony\Component\Validator\Constraints as Assert;

class MetaCommand
{
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string|null $title;

    #[Assert\Type('string')]
    public string|null $description;

    #[Assert\Type('string')]
    public string|null $keywords;

    public function __construct(Meta|null $meta = null)
    {
        if ($meta) {
            $this->title       = $meta->getTitle();
            $this->description = $meta->getDescription();
            $this->keywords    = $meta->getKeywords();
        }
    }
}