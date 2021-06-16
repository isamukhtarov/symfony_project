<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository")
 * @ORM\Table(name="photos")
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=10, options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $filename;

    /**
     * @ORM\Column(type="string")
     */
    private string $original_filename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $gradient_rgb;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $resolution;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $created_at;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PhotoBundle\Entity\Translation",
     *     mappedBy="photo",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
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

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->original_filename = $originalFilename;
        return $this;
    }

    public function setGradientRgb(string $gradientRgb): self
    {
        $this->gradient_rgb = $gradientRgb;
        return $this;
    }

    public function getTranslation(string $language): ?Translation
    {
        return $this->translations[$language];
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[$translation->getLanguage()] = $translation;
        }
        $translation->setPhoto($this);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): self
    {
        $this->resolution = $resolution;
        return $this;
    }

    public function getGradientRgb(): string
    {
        return $this->gradient_rgb;
    }
}
