<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\City;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Table(name="cities")
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Query\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Translation",
     *     mappedBy="city",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     *     )
     */
    private Collection $translations;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post",
     *     mappedBy="city",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     *     )
     */
    private Collection $posts;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Region\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private Region $region;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getTranslation(string $language): ?Translation
    {
        return $this->translations->get($language);
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setCity($this);
        }

        return $this;
    }
}