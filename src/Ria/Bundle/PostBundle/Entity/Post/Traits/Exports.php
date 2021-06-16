<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PostBundle\Entity\Post\Export;

trait Exports
{
    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Post\Export",
     *     mappedBy="post",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *  )
     */
    private Collection $exports;

    public function getExports(): Collection
    {
        return $this->exports;
    }

    public function addExport(Export $export): self
    {
        if (!$this->exports->contains($export)) {
            $this->exports->add($export);
            $export->setPost($this);
        }

        return $this;
    }
}