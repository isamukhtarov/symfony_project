<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\PostBundle\Entity\Post\Note;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;

/**
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 */
class NoteRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }
}