<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class AbstractRepository extends ServiceEntityRepository
{
    // TODO: Declare constructor here and guess entity automatically.

    public function save(object $entity): void
    {
        // Check if this object actually is entity.
        try {
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (ORMException $e) {
            dd($e->getMessage()); // For development.
            // TODO: Log exception for production.
        }
    }

    public function remove(object $entity): void
    {
        // Check if this object actually is entity.
        try {
            $this->_em->remove($entity);
            $this->_em->flush();
        } catch (ORMException $e) {
            dd($e->getMessage()); // For development.
            // TODO: Log exception for production.
        }
    }

    private function guessEntityFQN(): string
    {
        return '';
    }
}