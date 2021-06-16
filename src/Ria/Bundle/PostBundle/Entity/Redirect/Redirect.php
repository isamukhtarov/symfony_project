<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Redirect;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="redirects")
 * @ORM\Entity()
 */
class Redirect
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="old_url")
     */
    private string $oldUrl;

    /**
     * @ORM\Column(type="string", name="new_url")
     */
    private string $newUrl;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOldUrl(): string
    {
        return $this->oldUrl;
    }

    public function getNewUrl(): string
    {
        return $this->newUrl;
    }

    public function setOldUrl(string $url): self
    {
        $this->oldUrl = $url;

        return $this;
    }

    public function setNewUrl(string $url): self
    {
        $this->newUrl = $url;

        return $this;
    }
}