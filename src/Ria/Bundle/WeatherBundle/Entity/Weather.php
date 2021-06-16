<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="weather")
 * @ORM\Entity(repositoryClass="Ria\Bundle\WeatherBundle\Repository\WeatherRepository")
 */
class Weather
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="text", name="data")
     */
    private string $data;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
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
     * @return array
     */
    public function getData(): array
    {
        return json_decode($this->data, true);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData(string $data): self
    {
        $this->data = $data;
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
}