<?php

namespace Ria\Bundle\PostBundle\Entity\Category\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\CoreBundle\Entity\Meta;

trait TranslationLifecycleCallbacks
{
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function prePersist()
    {
//        $this->meta = $this->getMeta()->encode();
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
//        $this->meta = Meta::fromJson($this->meta);
    }
}