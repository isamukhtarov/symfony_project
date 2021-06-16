<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Handler;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostDateChanged;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostDateChangedHandler implements MessageHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private PostRepository $postRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function __invoke(PostDateChanged $message)
    {
        try {
            $post = $this->postRepository->find($message->getPostId());
            $this->mailer->send((new TemplatedEmail())
                ->subject("Post's date was changed [{$this->parameterBag->get('domain')}]")
                ->htmlTemplate('@RiaPost/emails/post-date-changed.html.twig')
                ->context(['post' => $post, 'cause' => $message->getCause()]));
        } catch (TransportExceptionInterface | Exception $e) {
            $this->logger->error('Occurred error, while sending mail about changing post date.', compact('e'));
        }
    }
}