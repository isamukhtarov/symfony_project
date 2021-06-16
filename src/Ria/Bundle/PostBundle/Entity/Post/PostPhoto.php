<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="post_photo")
 */
class PostPhoto
{
    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     */
    private ?Photo $photo;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="photoRelation")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     */
    private ?Post $post;

    /**
     * @ORM\Column(type="integer")
     */
    private int $sort;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost($post): self
    {
        $this->post = $post;
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