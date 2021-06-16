<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="post_tag")
 */
class Tag
{
    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Tag\Tag", inversedBy="postAssignments")
     * @ORM\JoinColumn(name="tag_name", referencedColumnName="name", onDelete="CASCADE")
     */
    private Tag $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="tagAssignments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post $post;
}