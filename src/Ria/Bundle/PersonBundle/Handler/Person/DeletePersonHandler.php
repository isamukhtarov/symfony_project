<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Handler\Person;



use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PersonBundle\Command\Person\DeletePersonCommand;;
use Ria\Bundle\PersonBundle\Entity\Person\Person;

/**
 * Class DeletePersonHandler
 * @package Ria\Bundle\PersonBundle\Handler\Person
 */
class DeletePersonHandler
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param DeletePersonCommand $command
     */
    public function handle(DeletePersonCommand $command) : void
    {
        $person = $this->entityManager->find(Person::class, $command->id);

        $this->entityManager->remove($person);
        $this->entityManager->flush();
    }
}