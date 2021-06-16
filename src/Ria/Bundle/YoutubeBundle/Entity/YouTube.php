<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class YouTube
 * @package Ria\Bundle\YoutubeBundle\Entity
 * @ORM\Table(name="youtube_videos")
 * @ORM\Entity()
 */
class YouTube
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="youtube_id", nullable=false)
     */
    private string $youtubeId;

    /**
     * @ORM\Column (type="boolean")
     */
    private bool $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $youtubeId
     * @return $this
     */
    public function setYoutubeId(string $youtubeId): self
    {
        $this->youtubeId = $youtubeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeId(): string
    {
        return $this->youtubeId;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }
}