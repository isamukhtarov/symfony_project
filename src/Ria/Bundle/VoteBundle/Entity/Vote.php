<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Entity;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\CoreBundle\Entity\HasRelations;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="votes")
 * @ORM\Entity(repositoryClass="Ria\Bundle\VoteBundle\Repository\VoteRepository")
 */
class Vote
{
    use HasRelations;

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
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private bool $status;

    /**
     * @ORM\Embedded(class="Ria\Bundle\VoteBundle\Entity\Type", columnPrefix=false)
     */
    private Type $type;

    /**
     * @ORM\Column(type="boolean", name="show_recaptcha", options={"default" : 0, "unsigned"=true})
     */
    private bool $showRecaptcha;

    /**
     * @ORM\Column(type="boolean", name="show_on_main")
     */
    private bool $showOnMainPage;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private string $language;

    /**
     * @ORM\Column(type="datetime", name="start_date", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $startDate;

    /**
     * @ORM\Column(type="datetime", name="end_date", nullable=true)
     */
    private DateTime|null $endDate;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private Photo|null $photo;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\VoteBundle\Entity\Option",
     *     mappedBy="vote",
     *     cascade={"persist", "remove"},
     *     indexBy="sort",
     *     orphanRemoval=true
     *     )
     */
    private Collection $options;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\VoteBundle\Entity\Log",
     *     mappedBy="vote",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private Collection $logs;

    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", mappedBy="votes")
     */
    private Collection $posts;

    #[Pure]
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function showRecaptcha(): bool
    {
        return $this->showRecaptcha;
    }

    public function setShowRecaptcha(bool $showRecaptcha): self
    {
        $this->showRecaptcha = $showRecaptcha;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function showOnMainPage(): bool
    {
        return $this->showOnMainPage;
    }

    public function setShowOnMainPage(bool $showOnMainPage): self
    {
        $this->showOnMainPage = $showOnMainPage;
        return $this;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setVote($this);
        }

        return $this;
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setVote($this);
        }

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}