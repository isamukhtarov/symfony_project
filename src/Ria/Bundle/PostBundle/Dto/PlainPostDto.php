<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Dto;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PostBundle\Entity\Tag\Tag;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\Export;
use Ria\Bundle\PersonBundle\Entity\Person\Person;

class PlainPostDto
{
    private function __construct(
        private int $id,
        private int $parentId,
        private string $language,
        private ?string $icon,
        private string $type,
        private ?string $optionType,
        private string $categoryTitle,
        private string $categorySlug,
        private int $createdBy,
        private ?string $author,
        private ?string $city,
        private ?string $story,
        private ?string $youtubeId,
        private string $title,
        private ?string $description,
        private string $content,
        private ?string $source,
        private string $publishedAt,
        private string $slug,
        private string $status,
        private array $markedWords,
        private ?string $photo,
        private bool $isMain,
        private bool $isExclusive,
        private bool $isActual,
        private bool $isBreaking,
        private bool $isImportant,
        private array $meta = [],
        private array $tags = [],
        private array $persons = [],
        private array $exports = [],
        private array $relatedPosts = [],
        private array $photos = [],
    ){}

    public static function fromEntity(Post $post): self
    {
        $category = $post->getCategory()->getTranslation($post->getLanguage());

        return new self(
            $post->getId(),
            $post->getParent()->getId(),
            $post->getLanguage(),
            $post->getIcon() ?? '',
            $post->getType()->toValue(),
            $post->getOptionType() ?? '',
            $category->getTitle(),
            $category->getSlug(),
            $post->getCreatedBy(),
            $post->getAuthor()?->getTranslation($post->getLanguage())->getFullName(),
            $post->getCity()?->getTranslation($post->getLanguage())->getTitle(),
            $post->getStory()?->getTranslation($post->getLanguage())->getTitle(),
            $post->getYoutubeId(),
            $post->getTitle(),
            $post->getDescription() ?? '',
            $post->getContent() ?? '',
            $post->getSource() ?? '',
            $post->getPublishedAt()->format('Y-m-d H:i:s'),
            $post->getSlug(),
            $post->getStatus()->toValue(),
            $post->getMarkedWords(),
            $post->getPhoto()?->getFilename(),
            $post->isMain(),
            $post->isExclusive(),
            $post->isActual(),
            $post->isBreaking(),
            $post->isImportant(),
            $post->getMeta()->toArray(),
            array_map(fn (Tag $tag) => $tag->getTranslation($post->getLanguage())?->getName(), $post->getTags()->toArray()),
            array_map(fn (Person $person) => $person->getTranslation($post->getLanguage())?->getFullName(), $post->getPersons()->toArray()),
            array_map(fn (Export $export) => $export->toValue(), $post->getExports()->toArray()),
            array_map(fn (Post $related) => $related->getTitle(), $post->getRelatedPosts()->toArray()),
            array_map(fn (Photo $photo) => $photo->getFilename(), $post->getPhotos()->toArray()),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptionType(): ?string
    {
        return $this->optionType;
    }

    public function getCategoryTitle(): string
    {
        return $this->categoryTitle;
    }

    public function getCategorySlug(): string
    {
        return $this->categorySlug;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStory(): ?string
    {
        return $this->story;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getPublishedAt(): string
    {
        return $this->publishedAt;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMarkedWords(): array
    {
        return $this->markedWords;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function isMain(): bool
    {
        return $this->isMain;
    }

    public function isExclusive(): bool
    {
        return $this->isExclusive;
    }

    public function isActual(): bool
    {
        return $this->isActual;
    }

    public function isBreaking(): bool
    {
        return $this->isBreaking;
    }

    public function isImportant(): bool
    {
        return $this->isImportant;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getPersons(): array
    {
        return $this->persons;
    }

    public function getExports(): array
    {
        return $this->exports;
    }

    public function getRelatedPosts(): array
    {
        return $this->relatedPosts;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }
}