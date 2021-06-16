<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Entity\Person;

use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\PhotoBundle\Entity\Photo;

/**
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="person_photo")
 */
class PersonPhoto
{
    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     * @ORM\Id()
     */
    private Photo|null $photo;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\Person", inversedBy="photoRelations")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * @ORM\Id()
     */
    private Person|null $person;

    /**
     * @ORM\Column(type="integer")
     */
    private int $sort;

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson($person): self
    {
        $this->person = $person;
        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;
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