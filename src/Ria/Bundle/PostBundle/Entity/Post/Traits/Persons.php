<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PostBundle\Entity\Post\Post;

/**
 * Class Persons
 * @package Ria\News\Core\Models\Post\Traits
 */
trait Persons
{
    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\Person", inversedBy="posts", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="post_person")
     */
    private Collection $persons;

    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->persons->contains($person)) {
            $this->persons->add($person);
            /** @var Post $this */
            $person->addPost($this);
        }

        return $this;
    }


    public function removePerson(Person $person): self
    {
        if ($this->persons->contains($person)) {
            $this->persons->removeElement($person);
            /** @var Post $this */
            $person->removePost($this);
        }

        return $this;
    }
}