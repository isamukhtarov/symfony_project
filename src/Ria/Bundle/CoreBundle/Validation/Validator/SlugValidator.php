<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\PostBundle\Command\Post\{CreatePostCommand, UpdatePostCommand, AbstractPostCommand};

class SlugValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[a-z0-9-]*$/s', $value))
            $this->context->buildViolation('Invalid slug !')->addViolation();

        /** @var AbstractPostCommand $command */
        $command = $this->context->getObject();
        $postDto = $command->getPostDto();

        if ($command instanceof CreatePostCommand) {
            if ($this->checkUniquenessOfSlug($value, $postDto->getLanguage()) > 0)
                $this->context->buildViolation('This slug is already in use.')->addViolation();
            return;
        }

        if ($command instanceof UpdatePostCommand) {
            $post = $postDto->getPost();
            if ($post->getStatus()->isActive() && strcmp($post->getSlug(), $value) !== 0)
                $this->context->buildViolation('You cannot change post url when post is active.')->addViolation();

            if ($this->checkUniquenessOfSlug($value, $postDto->getLanguage(), $post->getId()) > 0)
                $this->context->buildViolation('This slug is already in use.')->addViolation();
            return;
        }
    }

    private function checkUniquenessOfSlug(string $slug, string $language, ?int $postId = null): int
    {
        $repository = $this->entityManager->getRepository(Post::class);
        $query = $repository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.slug = :slug and p.language = :language')
            ->setParameters(compact('slug', 'language'));

        if ($postId !== null)
            $query->andWhere($query->expr()->not($query->expr()->eq('p.id', ':post_id')))->setParameter('post_id', $postId);

        return (int) $query->getQuery()->getSingleScalarResult();
    }
}