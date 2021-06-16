<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Exception;
use Psr\Log\LoggerInterface;
use Ria\Bundle\PostBundle\Query\PostIndexer;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostDeleted;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostDeletedSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private PostIndexer $postIndexer,
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function __invoke(PostDeleted $message)
    {
        $post = $this->postRepository->find($message->getPostId());
        $this->postIndexer->remove($post);
        $this->notifyViaEmail($this->userRepository->find($message->getUserId()), $post, $message->getCause());
    }

    private function notifyViaEmail(User $user, Post $post, string $cause): void
    {
        try {
            $this->mailer->send((new TemplatedEmail())
                ->subject("Post deactivated [{$this->parameterBag->get('domain')}]")
                ->htmlTemplate('@RiaPost/emails/post-deleted.html.twig')
                ->context(compact('user', 'post', 'cause')));
        } catch (TransportExceptionInterface | Exception $e) {
            $this->logger->error('Occurred error, while sending mail about deleting post.', compact('e'));
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield PostDeleted::class => ['priority' => 100];
    }
}