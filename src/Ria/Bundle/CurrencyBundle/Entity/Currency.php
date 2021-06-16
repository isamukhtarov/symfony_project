<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="currencies")
 * @ORM\Entity(repositoryClass="Ria\Bundle\CurrencyBundle\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=10, options={"unsigned":true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private string $code;

    /**
     * @ORM\Column(type="float", nullable=false, options={"unsigned":true, "default":0})
     */
    private float $value;

    /**
     * @ORM\Column(type="smallint", length=3, options={"unsigned":true, "default":1})
     */
    private int $nominal;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default":0})
     */
    private float $difference;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private DateTime $date;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP")
     */
    private DateTime $updatedAt;

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getNominal(): int
    {
        return $this->nominal;
    }

    /**
     * @param int $nominal
     * @return $this
     */
    public function setNominal(int $nominal): self
    {
        $this->nominal = $nominal;
        return $this;
    }

    /**
     * @return float
     */
    public function getDifference(): float
    {
        return $this->difference;
    }

    /**
     * @param float $difference
     * @return $this
     */
    public function setDifference(float $difference): self
    {
        $this->difference = $difference;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}