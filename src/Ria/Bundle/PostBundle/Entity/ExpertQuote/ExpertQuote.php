<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\ExpertQuote;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PersonBundle\Entity\Person\Person;

/**
 * @ORM\Table(name="expert_quotes")
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Repository\ExpertQuoteRepository")
 */
class ExpertQuote
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\Person")
     * @JoinColumn(name="expert_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Person $expert;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post")
     * @JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post|null $post;

    /**
     * @ORM\Column(type="text")
     */
    private string $text;

    /**
     * @ORM\Column(type="datetime",
     *     name="created_at",
     *     columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
     *     nullable=false
     * )
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime",
     *     name="updated_at",
     *     columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
     *     nullable=false
     * )
     */
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getExpert(): Person
    {
        return $this->expert;
    }

    public function setExpert(Person $expert): self
    {
        $this->expert = $expert;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}