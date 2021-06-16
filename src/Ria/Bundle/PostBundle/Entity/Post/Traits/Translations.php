<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PostBundle\Entity\Post\Post;

trait Translations
{
    /**
     * @OneToMany(targetEntity="Post", mappedBy="parent", indexBy="language")
     */
    private Collection $translations;

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $language): ?Post
    {
        return $this->translations->get($language);
    }
}