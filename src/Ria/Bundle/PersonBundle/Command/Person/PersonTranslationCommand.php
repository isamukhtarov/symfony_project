<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Command\Person;

use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Translation;
use Symfony\Component\Validator\Constraints as Assert;

class PersonTranslationCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $first_name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $last_name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $slug;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $position;

    #[Assert\Type('string')]
    public string|null $text;

    #[Assert\NotBlank]
    public string $locale;

    #[Assert\Valid]
    public MetaCommand $meta;


    public function __construct(string $locale, Translation $translation = null)
    {
        $this->locale = $locale;

        if ($translation) {
            $this->first_name = $translation->getFirstName();
            $this->last_name  = $translation->getLastName();
            $this->slug = $translation->getSlug();
            $this->position = $translation->getPosition();
            $this->text = $translation->getText();
            $this->meta = new MetaCommand($translation->getMeta());
        } else {
            $this->meta = new MetaCommand();
        }
    }

}