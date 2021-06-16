<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Widget;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="media_widgets")
 * @ORM\Entity
 */
class Widget
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=10, options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\Column(type="string", columnDefinition="enum('youtube','facebook','twitter','instagram','vk','playbuzz','other')")
     */
    private Type $type;

    /**
     * @ORM\Column(type="datetime",
     *     name="created_at",
     *     columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
     *     nullable=false
     * )
     */
    private DateTime $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Widget
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     * @return Widget
     */
    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

}