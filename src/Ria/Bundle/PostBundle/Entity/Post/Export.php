<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Table(name="post_exports")
 * @ORM\Entity
 */
class Export
{
    const COMMON             = 'common';
    const YANDEX_DZEN        = 'yandex_dzen';
    const PUSH_NOTIFICATIONS = 'push_notifications';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $type;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="exports")
     * @JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post $post;

    public static function all(): array
    {
        return [
            self::COMMON,
            self::YANDEX_DZEN,
            self::PUSH_NOTIFICATIONS
        ];
    }

    public function toValue(): string
    {
        return $this->type;
    }

    public function is(string $type): bool
    {
        return $this->type === $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (in_array($type, self::all()))
            $this->type = $type;

        return $this;
    }

    public function setPost($post): self
    {
        $this->post = $post;

        return $this;
    }
}