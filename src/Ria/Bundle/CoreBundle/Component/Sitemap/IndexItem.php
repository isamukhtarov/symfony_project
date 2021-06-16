<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\Sitemap;

class IndexItem
{
    public $location;
    public $lastModified;

    public function __construct($location, $lastModified = null)
    {
        $this->location = $location;
        $this->lastModified = $lastModified;
    }
}