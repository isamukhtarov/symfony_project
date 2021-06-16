<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\PostBundle\Validation\Constraint\ValidArchiveLink;
use Ria\Bundle\CoreBundle\Component\CommandValidator\Validatable;

class ArchivePostCommand implements Validatable
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    //#[ValidArchiveLink]
    public string $link;

    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}