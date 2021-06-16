<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Region;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Table(name="regions")
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Query\Repository\RegionRepository")
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $sort;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Translation",
     *     mappedBy="region",
     *     cascade={"persist", "remove"},
     *     indexBy="language",
     *     fetch="EAGER"
     *     )
     */
    private Collection $translations;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $language): Translation|null
    {
        return $this->translations[$language];
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->getTranslations()->contains($translation)) {
            $this->translations[$translation->getLanguage()] = $translation;
            $translation->setRegion($this);
        }

        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }
    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}