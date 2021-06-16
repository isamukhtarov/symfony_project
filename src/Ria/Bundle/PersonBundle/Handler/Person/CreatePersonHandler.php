<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Handler\Person;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PersonBundle\Command\Person\CreatePersonCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Entity\Person\PersonPhoto;
use Ria\Bundle\PersonBundle\Entity\Person\Translation;
use Ria\Bundle\PhotoBundle\Entity\Photo;

class CreatePersonHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    #[NoReturn] public function handle(CreatePersonCommand $command) : void
    {
        $person = new Person();
        $mainPhoto = !$command->photos->main ? null : $this->entityManager->getReference(Photo::class, $command->photos->main);

        $person
            ->setStatus($command->status)
            ->setType($command->type)
            ->setPhoto($mainPhoto)
            ->sync('photoRelations', $this->preparePhotos($person, $command->photos->photos));

        if ($command->photo) {
            $person->setPhoto($this->entityManager->getPartialReference(Photo::class, $command->photo));
        }

        foreach ($command->translations as $translationCommand) {
            $translation = new Translation;

            $translation
                ->setFirstName($translationCommand->first_name)
                ->setLastName($translationCommand->last_name)
                ->setSlug($translationCommand->slug)
                ->setText($translationCommand->text ?? '')
                ->setPosition($translationCommand->position)
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array)$translationCommand->meta));

            $person->addTranslation($translation);
        }

        $this->entityManager->persist($person);
        $this->entityManager->flush();
    }

    private function preparePhotos(Person $person, array $photos): ArrayCollection
    {
        $photoRelations = new ArrayCollection();

        foreach ($photos as $i => $photo) {
            $photoRelation = new PersonPhoto();
            $photoRelation
                ->setPhoto($this->entityManager->getReference(Photo::class, $photo))
                ->setPerson($person)
                ->setSort($i + 1);

            $photoRelations->add($photoRelation);
        }

        return $photoRelations;
    }

}