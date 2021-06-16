<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Ria\Bundle\PostBundle\Messenger\Message\PostDateChanged;
use Ria\Bundle\PostBundle\Command\Post\ChangePostDateCommand;

class ChangePostDateHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private PostRepository $postRepository,
        private MessageBusInterface $messageBus,
    ){}

    public function handle(ChangePostDateCommand $command): void
    {
        $post = $command->getPost();
        try {
            $post->setPublishedAt(new DateTime($command->date))
                ->setIsPublished(($post->getPublishedAt()->getTimestamp() <= time()));
            $this->postRepository->save($post);
            $this->messageBus->dispatch(new PostDateChanged($post->getId(), $command->cause));
        } catch (Exception $e) {
            $this->logger->error('Error occurred while changing post date', compact('e'));
        }
    }
}