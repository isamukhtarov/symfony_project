<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="vote_options")
 * @ORM\Entity
 */
class Option
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(name="sort", type="integer", nullable=false, options={"unsigned"=true})
     */
    private int $sort;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0, "unsigned"=true})
     */
    private int $score;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\VoteBundle\Entity\Vote", inversedBy="options")
     * @JoinColumn(name="vote_id", referencedColumnName="id", nullable=false)
     */
    private Vote $vote;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\VoteBundle\Entity\Log",
     *     mappedBy="option",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private Collection $logs;

    #[Pure]
    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     * @return $this
     */
    public function setSort(int $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     * @return $this
     */
    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementScore(): self
    {
        $this->score++;
        return $this;
    }

    /**
     * @return $this
     */
    public function decrementScore(): self
    {
        $this->score--;
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
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setOption($this);
        }

        return $this;
    }
}