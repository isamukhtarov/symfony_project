<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Entity\Person\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\PersonBundle\Entity\Person\PersonPhoto;

trait PhotosRelation
{
    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\PersonPhoto", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $photo_relations;

    public function getPhotos()
    {
        return $this->photo_relations->map(function (PersonPhoto $personPhoto) {
            return $personPhoto->getPhoto();
        });
    }

    public function getPhotoRelations(): Collection
    {
        return $this->photo_relations;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function photosAreEqual(array $ids)
    {
        if (count($ids) != $this->photo_relations->count()) return false;

        foreach ($ids as $i => $id) {
            if ($id != $this->photo_relations[$i]->getPhoto()->getId()) return false;
        }

        return true;
    }

    public function syncPhotoRelations(Collection $photoRelations): self
    {
        if ($photoRelations->isEmpty()) {
            $this->photo_relations->clear();
            return $this;
        }

        foreach ($this->getPhotoRelationsForRemoval($photoRelations) as $item) {
            $this->photo_relations->removeElement($item);
        }

        foreach ($photoRelations as $relation) {
            if (!$this->photo_relations->offsetExists($relation->getPhoto()->getId())) {
                $this->photo_relations->add($relation);
            }
        }

        return $this;
    }

    protected function getPhotoRelationsForRemoval(Collection $photoRelations)
    {
        $selfIds = $this->photo_relations->map(function ($relation) {
            /** @var PersonPhoto $relation */
            return $relation->getPhoto()->getId();
        });

        if ($selfIds->isEmpty()) {
            return [];
        }

        $alienIds  = $photoRelations->map(function ($relation) {
            /** @var PersonPhoto $relation */
            return $relation->getPhoto()->getId();
        });

        $unusedIds = array_diff($selfIds->toArray(), $alienIds->toArray());

        return $this->photo_relations->filter(function ($relation) use ($unusedIds) {
            return in_array($relation->getPhoto()->getId(), $unusedIds);
        });
    }

    public function setPhotoRelations(Collection $photo_relations)
    {
        $this->photo_relations = $photo_relations;
        return $this;
    }
}