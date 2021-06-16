<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PostBundle\Entity\Post\PostPhoto;

trait Photos
{
    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\PostPhoto", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private Collection $photoRelation;

    public function getPhotos(): Collection
    {
        return $this->photoRelation->map(fn (PostPhoto $postPhoto) => $postPhoto->getPhoto());
    }

    public function getPhotoRelation(): Collection
    {
        return $this->photoRelation;
    }

}