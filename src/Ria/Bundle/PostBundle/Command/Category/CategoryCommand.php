<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

use Ria\Bundle\PostBundle\Entity\Category\Category;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryCommand
{
    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Valid]
    public ?Category $parent = null;

    #[Assert\Type('string')]
    public string $template;

    #[Assert\Valid]
    public array $translations;

    protected string $currentLocale;

    public function getCurrentLocale(): string
    {
        return $this->currentLocale;
    }
}