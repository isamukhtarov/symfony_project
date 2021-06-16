<?php

namespace Ria\Bundle\PostBundle\Entity\Category\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\PostBundle\Entity\Category\Template;

/**
 * Trait CategoryLifecycleCallbacks
 * @package Ria\News\Core\Models\Category\Traits
 * @property Template $template
 */
trait CategoryLifecycleCallbacks
{
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function prePersist()
    {
        $this->template = (string)$this->getTemplate();
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->template = new Template($this->template);
    }
}