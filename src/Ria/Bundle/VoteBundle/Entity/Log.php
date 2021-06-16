<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Table(name="vote_logs")
 * @ORM\Entity
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="user_agent", length=255, nullable=false)
     */
    private string $userAgent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $ip;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\VoteBundle\Entity\Vote", inversedBy="logs")
     * @JoinColumn(name="vote_id", referencedColumnName="id", nullable=false)
     */
    private Vote $vote;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\VoteBundle\Entity\Option", inversedBy="logs")
     * @JoinColumn(name="vote_option_id", referencedColumnName="id", nullable=false)
     */
    private Option $option;

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
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return Vote
     */
    public function getVote(): Vote
    {
        return $this->vote;
    }

    /**
     * @param Vote $vote
     * @return $this
     */
    public function setVote(Vote $vote): self
    {
        $this->vote = $vote;
        return $this;
    }

    /**
     * @return Option
     */
    public function getOption(): Option
    {
        return $this->option;
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function setOption(Option $option): self
    {
        $this->option = $option;
        return $this;
    }
}