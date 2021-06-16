<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity()
 * @ORM\Table(name="photos_lang")
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
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $information;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $author;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $source;

    /**
     * @ORM\Column(type="string")
     */
    private string $language;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo", inversedBy="translations")
     * @JoinColumn(name="photo_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Photo $photo;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function setPhoto(Photo $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;
        return $this;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;
        return $this;
    }

}
