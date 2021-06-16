<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Dto\PostDto;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\PostBundle\Validation\Constraint\TranslationAlreadyCreated;
use Ria\Bundle\PostBundle\Entity\{City\City, Story\Story, Post\Type, Category\Category};
use Ria\Bundle\CoreBundle\Validation\Constraint\{IfThen, Slug, Timestamp, ValidLinks, EntityExists};

abstract class AbstractPostCommand
{
    #[Assert\Type('string')]
    public ?string $icon = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public ?string $type = null;

    #[Assert\Type('string')]
    public ?string $optionType = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $source = null;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[EntityExists(Category::class)]
    public int $categoryId;

    /**
     * @Assert\Type("integer")
     * @IfThen("this.authorIsRequired()", constraints={@Assert\NotBlank})
     * @EntityExists(User::class)
     */
    public ?int $authorId = null;

    /**
     * @Assert\Type("integer")
     * @IfThen("this.expertIsRequired()", constraints={@Assert\NotBlank})
     * @EntityExists(Person::class)
     */
    public ?int $expertId = null;

    /**
     * @Assert\Type("integer")
     * @IfThen("this.isCreationOfTranslation()", constraints={@Assert\NotBlank})
     * @EntityExists(User::class)
     */
    public ?int $translatorId = null;

    #[Assert\Type('integer')]
    #[EntityExists(City::class)]
    public ?int $cityId = null;

    #[Assert\Type('integer')]
    public int $createdBy;

    #[Assert\Type('integer')]
    #[EntityExists(Story::class)]
    public ?int $storyId = null;

    #[Assert\Type('array')]
    public array $tags = [];

    #[Assert\Type('array')]
    public array $persons = [];

    #[Assert\Type('array')]
    public array $markedWords = [];

    #[Assert\Type('array')]
    public array $relatedPosts = [];

    #[Assert\Type('array')]
    public array $exports = [];

    #[Assert\Type('string')]
    #[Assert\Length(max: 11)]
    public ?string $youtubeId = null;

    #[Slug]
    #[Assert\Type('string')]
    #[Assert\Length(max: 150)]
    #[Assert\NotBlank]
    public string $slug;

    #[Assert\Type('string')]
    #[Assert\Length(max: 150)]
    #[Assert\NotBlank]
    public string $title;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[ValidLinks]
    #[Assert\Type('string')]
    public ?string $content = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $note = null;

    /**
     * @Assert\Type("string")
     * @IfThen("this.isCreationOfTranslation()", constraints={@TranslationAlreadyCreated})
     */
    public string $language;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $status;

    #[Assert\Type('string')]
    #[Timestamp]
    #[Assert\NotBlank]
    public string $publishedAt;

    #[Assert\Type('bool')]
    public bool $attachCurrentTime = false;

    #[Assert\Type('bool')]
    public bool $isMain = false;

    #[Assert\Type('bool')]
    public bool $isExclusive = false;

    #[Assert\Type('bool')]
    public bool $isActual = false;

    #[Assert\Type('bool')]
    public bool $isBreaking = false;

    #[Assert\Type('bool')]
    public bool $isImportant = false;

    #[Assert\Type('bool')]
    public bool $linksNoIndex = true;

    #[Assert\Valid]
    public ?PhotoCommand $photos = null;

    #[Assert\Valid]
    public MetaCommand $meta;

    public ?int $id = null;
    public bool $wasPublished = false;

    protected PostDto $postDto;

    private bool $isCreationOfTranslation = false;

    public function setCreationOfTranslation(bool $isCreationOfTranslation): void
    {
        $this->isCreationOfTranslation = $isCreationOfTranslation;
    }

    // Conditions for validation.
    public function isCreationOfTranslation(): bool
    {
        return $this->isCreationOfTranslation;
    }

    public function authorIsRequired(): bool
    {
        return $this->type !== Type::OPINION;
    }

    #[Pure] public function expertIsRequired(): bool
    {
        return !$this->authorIsRequired(); // Modify, if logic differs.
    }

    public function getPostDto(): PostDto
    {
        return $this->postDto;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this as $property => $value)
            $data[$property] = $value;
        return $data;
    }
}