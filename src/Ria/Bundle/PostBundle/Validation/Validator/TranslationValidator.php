<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Validation\Validator;

use InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Ria\Bundle\PostBundle\Entity\Post\{Post, Status};
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\PostBundle\Command\Post\CreatePostCommand;

class TranslationValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function validate($value, Constraint $constraint)
    {
        $command = $this->context->getObject();
        if (!($command instanceof CreatePostCommand) || !$command->isCreationOfTranslation())
            throw new InvalidArgumentException('This validation only works with translation logic !');

        $postDto = $command->getPostDto();
        $queryBuilder = $this->entityManager
            ->getRepository(Post::class)
            ->createQueryBuilder('p');

        $query = $queryBuilder
            ->select("count(p.id)")
            ->where('p.parent = :parent', 'p.language = :language')
            ->andWhere($queryBuilder->expr()->notIn('p.status.status', [Status::ARCHIVED, Status::DELETED]))
            ->setParameters(['parent' => $postDto->getPost()->getId(), 'language' => $command->language]);

        if ($postDto->hasPost())
            $query->andWhere($queryBuilder->expr()->neq('p.id', $postDto->getPost()->getId()));

        $translationExists = (bool) $query->getQuery()->getSingleScalarResult();

        if ($translationExists)
            $this->context->buildViolation('Translation on this language is already created.')->addViolation();
    }
}