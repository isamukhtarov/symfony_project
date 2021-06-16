<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\ExpertQuote;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PostBundle\Command\ExpertQuote\CreateExpertQuoteCommand;
use Ria\Bundle\PostBundle\Entity\ExpertQuote\ExpertQuote;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Repository\ExpertQuoteRepository;

class CreateExpertQuoteHandler
{
    public function __construct(
        private ExpertQuoteRepository $expertQuoteRepository,
        private EntityManagerInterface $entityManager
    ){}

    public function handle(CreateExpertQuoteCommand $command): void
    {
        $quote = new ExpertQuote();
        $expert = $this->entityManager->find(Person::class, $command->expertId);
        $post = empty($command->postId) ? null : $this->entityManager->find(Post::class, $command->postId);

        $quote->setExpert($expert)
              ->setPost($post)
              ->setText($command->text);

        $this->expertQuoteRepository->save($quote);
    }
}