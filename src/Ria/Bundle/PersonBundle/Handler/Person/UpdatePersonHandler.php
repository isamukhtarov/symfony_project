<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Handler\Person;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PersonBundle\Command\Person\UpdatePersonCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Entity\Person\PersonPhoto;
use Ria\Bundle\PhotoBundle\Entity\Photo;

class UpdatePersonHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function handle(UpdatePersonCommand $command) : void
    {
        /** @var Person $person */
        $person = $this->entityManager->find(Person::class, $command->id);
        $mainPhoto = !$command->photos->main ? null : $this->entityManager->getReference(Photo::class, $command->photos->main);
        $photo = !$command->photo ? null : $this->entityManager->getPartialReference(Photo::class, $command->photo);

        $person
            ->setStatus($command->status)
            ->setPhoto($mainPhoto ?? $photo)
            ->sync('photoRelations', $this->preparePhotos($person, $command->photos->photos));

        foreach ($command->translations as $translationCommand) {
            $translation = $person->getTranslation($translationCommand->locale);

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

        $this->entityManager->flush();
    }

    private function preparePhotos(Person $person, array $photos): ArrayCollection
    {
        $photoRelations = new ArrayCollection();

        foreach ($photos as $i => $photoId) {
            $photoRelation = $person->getPhotoRelation()
                ->filter(fn (PersonPhoto $personPhoto) => $personPhoto->getPhoto()->getId() == $photoId)->first();

            if (!$photoRelation) {
                $photoRelation = (new PersonPhoto())
                    ->setPhoto($this->entityManager->getReference(Photo::class, $photoId))
                    ->setPerson($person);
            }

            $photoRelation->setSort($i + 1);
            $photoRelations->add($photoRelation);
        }

        return $photoRelations;
    }
}