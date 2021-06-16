<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\City;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="cities_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 * @ORM\Entity
 */
class Translation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
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
    private string $slug;

    /**
     * @ORM\Column(type="string")
     */
    public string $language;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\City\City", inversedBy="translations")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private City $city;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }
}