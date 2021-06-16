<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PostBundle\Entity\City\City;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\{JoinColumn, ManyToOne};
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\CoreBundle\Entity\{HasRelations, Meta};
use Ria\Bundle\PostBundle\Entity\Post\Traits\{Persons, Tags, Related, Exports, Photos, Logs, Translations, Votes};

/**
 * @ORM\Table(name="posts", uniqueConstraints={@ORM\UniqueConstraint(name="slug_unique", columns={"slug", "language"})})
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Repository\PostRepository")
 */
class Post
{
    use HasRelations,
        Translations,
        Photos,
        Related,
        Exports,
        Persons,
        Logs,
        Tags,
        Votes;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ManyToOne(targetEntity="Post", inversedBy="translations")
     * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private ?Post $parent = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Category\Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="translator_id", referencedColumnName="id")
     */
    private ?User $translator = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private User $author;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Story\Story", inversedBy="posts")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    private ?Story $story;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\City\City", inversedBy="posts")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private ?City $city;

    /**
     * @ORM\OneToOne(targetEntity="Speech", mappedBy="post")
     */
    private Speech $speech;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\Person", inversedBy="posts")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id")
     */
    private ?Person $expert = null;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private ?string $youtubeId = null;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private string $language;

    /**
     * @ORM\Column(type="integer", name="created_by")
     */
    private int $createdBy;

    /**
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Photo $photo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private array $markedWords = [];

    /**
     * @ORM\Embedded(class="Ria\Bundle\PostBundle\Entity\Post\Type", columnPrefix=false)
     */
    private Type $type;

    /**
     * @ORM\Embedded(class="Ria\Bundle\PostBundle\Entity\Post\Status", columnPrefix=false)
     */
    private Status $status;

    /**
     * @ORM\Column(type="string", name="option_type", nullable=true, columnDefinition="ENUM('ads', 'closed')")
     */
    private ?string $optionType = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $source = null;

    /**
     * @ORM\Column(type="boolean", name="is_published", options={"default" : 0})
     */
    private bool $isPublished = false;

    /**
     * @ORM\Column(type="boolean", name="is_deactivated", nullable=true, options={"default" : 0})
     */
    private ?bool $isDeactivated = false;

    /**
     * @ORM\Column(type="boolean", name="is_main", options={"default" : 0})
     */
    private bool $isMain = false;

    /**
     * @ORM\Column(type="boolean", name="is_exclusive", options={"default" : 0})
     */
    private bool $isExclusive = false;

    /**
     * @ORM\Column(type="boolean", name="is_actual", options={"default" : 0})
     */
    private bool $isActual = false;

    /**
     * @ORM\Column(type="boolean", name="is_breaking", options={"default" : 0})
     */
    private bool $isBreaking = false;

    /**
     * @ORM\Column(type="boolean", name="is_important", options={"default" : 0})
     */
    private bool $isImportant = false;

    /**
     * @ORM\Column(type="integer", options={"default" : 0, "unsigned"=true})
     */
    private int $views = 0;

    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Meta", columnPrefix=false)
     */
    private Meta $meta;

    /**
     * @ORM\Column(type="boolean", name="links_no_index", options={"default" : 1})
     */
    private bool $linksNoIndex = true;

    /**
     * @ORM\Column(type="datetime", name="published_at", nullable=true)
     */
    private DateTime $publishedAt;

    /**
     * @ORM\Column(type="datetime",
     *     name="created_at",
     *     columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
     *     nullable=false
     * )
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime",
     *     name="updated_at",
     *     columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
     *     nullable=false
     * )
     */
    private DateTime $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Notification", mappedBy="post")
     */
    private ?Notification $notification = null;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->relatedPosts = new ArrayCollection();
        $this->exports = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->photoRelation = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?Post $post): self
    {
        $this->parent = $post;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTranslator(): ?User
    {
        return $this->translator;
    }

    public function setTranslator(?User $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): self
    {
        $this->story = $story;

        return $this;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(?string $youtubeId): self
    {
        $this->youtubeId = $youtubeId;

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

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function getMarkedWords(): array
    {
        return $this->markedWords;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setMarkedWords(array $markedWords): self
    {
        $this->markedWords = $markedWords;

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

    public function getExpert(): ?Person
    {
        return $this->expert;
    }

    public function setExpert(?Person $expert): self
    {
        $this->expert = $expert;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOptionType(): ?string
    {
        return $this->optionType;
    }

    public function setOptionType(?string $optionType): self
    {
        $this->optionType = $optionType;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function isDeactivated(): bool
    {
        return $this->isDeactivated;
    }

    public function setIsDeactivated(bool $isDeactivated): self
    {
        $this->isDeactivated = $isDeactivated;

        return $this;
    }

    public function isMain(): bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function isExclusive(): bool
    {
        return $this->isExclusive;
    }

    public function setIsExclusive(bool $isExclusive): self
    {
        $this->isExclusive = $isExclusive;

        return $this;
    }

    public function isActual(): bool
    {
        return $this->isActual;
    }

    public function setIsActual(bool $isActual): self
    {
        $this->isActual = $isActual;

        return $this;
    }

    public function isBreaking(): bool
    {
        return $this->isBreaking;
    }

    public function setIsBreaking(bool $isBreaking): self
    {
        $this->isBreaking = $isBreaking;

        return $this;
    }

    public function isImportant(): bool
    {
        return $this->isImportant;
    }

    public function setIsImportant(bool $isImportant): self
    {
        $this->isImportant = $isImportant;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function isLinksNoIndex(): bool
    {
        return $this->linksNoIndex;
    }

    public function setLinksNoIndex(bool $linksNoIndex): self
    {
        $this->linksNoIndex = $linksNoIndex;

        return $this;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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