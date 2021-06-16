<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Entity\Person;

use Ria\Bundle\PostBundle\Entity\Category\Traits\TranslationLifecycleCallbacks;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Translation
 * @package Ria\Bundle\PersonBundle\Entity\Person
 * @ORM\Table(name="persons_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
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
    private string $first_name;
    /**
     * @ORM\Column(type="string")
     */
    private string $last_name;
    /**
     * @ORM\Column(type="string")
     */
    private string $slug;
    /**
     * @ORM\Column(type="string")
     */
    private string $position;
    /**
     * @ORM\Column(type="text")
     */
    public ?string $text;
    /**
     * @ORM\Column(type="string")
     */
    public string $language;
    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Meta", columnPrefix=false)
     * @var Meta
     */
    private Meta $meta;
    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\Person", inversedBy="translations")
     * @JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Person $person;

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->first_name;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName) : self
    {
        $this->first_name = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return $this->last_name;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName) : self
    {
        $this->last_name = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language) : self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson() : Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return $this
     */
    public function setPerson(Person $person): self
    {
        $this->person = $person;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return Meta
     */
    public function getMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @param Meta $meta
     * @return $this
     */
    public function setMeta(Meta $meta) : self
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition() : string
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return Translation
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
}