<?php

namespace Ria\Bundle\PostBundle\Entity\Region;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="regions_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 * @ORM\Entity
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="string")
     */
    public string $language;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Region\Region", inversedBy="translations")
     * @JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Region $region;

    /**
     * @ORM\Column(type="string")
     */
    private string $slug;

    public function set(string $title, string $slug, string $language): self
    {
        $this->title       = $title;
        $this->slug        = $slug;
        $this->language    = $language;

        return $this;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }




}