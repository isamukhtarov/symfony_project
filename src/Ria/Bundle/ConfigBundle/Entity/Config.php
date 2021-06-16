<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Config
 * @package Ria\Bundle\ConfigBundle\Entity
 * @ORM\Table(name="config")
 * @ORM\Entity()
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $param;

    /**
     * @ORM\Column(type="string")
     */
    private string $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string|null $value;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string|null $default;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string|null $label;

    /**
     * @return string
     */
    public function getParam(): string
    {
        return $this->param;
    }

    /**
     * @param string $param
     * @return $this
     */
    public function setParam(string $param): self
    {
        $this->param = $param;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->value !== '';
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param string|null $default
     * @return $this
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return $this
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}