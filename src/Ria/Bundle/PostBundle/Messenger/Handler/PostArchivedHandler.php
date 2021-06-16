<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Handler;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Ria\Bundle\PostBundle\Entity\Redirect\Redirect;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostArchived;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostArchivedHandler implements MessageHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private PostRepository $postRepository,
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager,
    ){}

    public function __invoke(PostArchived $message): void
    {
        if (($post = $this->postRepository->find($message->getPostId())) === null) return;

        $category = $post->getCategory()->getTranslation($post->getLanguage());
        $oldUrl = $this->urlGenerator->generate('post_view', [
            'categorySlug' => $category->getSlug(), 'slug' => $post->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $alreadyExisting = $this->entityManager->getRepository(Redirect::class)
            ->findOneBy(compact('oldUrl'));

        $redirect = $alreadyExisting ?: (new Redirect())->setOldUrl($oldUrl);
        $this->entityManager->persist($redirect->setNewUrl($message->getLink()));
        $this->entityManager->flush();

        $this->notifyViaEmail($post, $redirect);
    }

    private function notifyViaEmail(Post $post, Redirect $redirect): void
    {
        try {
            $this->mailer->send((new TemplatedEmail())
                ->subject("Post archived [{$this->parameterBag->get('domain')}]")
                ->htmlTemplate('@RiaPost/emails/post-archived.html.twig')
                ->context(['post' => $post, 'redirectUrl' => $redirect->getNewUrl()]));
        } catch (TransportExceptionInterface | Exception $e) {
            $this->logger->error('Occurred error, while sending mail about archiving post.', compact('e'));
        }
    }
}