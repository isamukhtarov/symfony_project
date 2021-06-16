<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Entity\Category\Traits\TranslationLifecycleCallbacks;

/**
 * @ORM\Table(name="categories_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="string")
     */
    public string $language;

    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Meta", columnPrefix=false)
     * @var Meta
     */
    private Meta $meta;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="translations")
     * @JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Category $category;

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $language
     * @return Translation
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }
}