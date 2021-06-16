<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Table(name="post_views")
 * @ORM\Entity
 */
class Views
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", options={"default" : 0, "unsigned"=true})
     */
    private int $views;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="post")
     * @JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    private Post $postId;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private string $ip;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;
}