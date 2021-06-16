<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Validation\Constraint\Timestamp;

class ChangePostDateCommand
{
    #[Assert\NotBlank]
    #[Timestamp]
    public string $date;

    #[Assert\NotBlank]
    public string $cause;

    private Post $post;

    public function __construct(Post $post)
    {
        $this->date = $post->getPublishedAt()->format('Y-m-d H:i:s');
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}