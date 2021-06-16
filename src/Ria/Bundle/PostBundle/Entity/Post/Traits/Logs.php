<?php

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait Logs
{
    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Log\Log", mappedBy="post")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     */
    private Collection $logs;

    public function getLogs(): Collection
    {
        return $this->logs;
    }
}