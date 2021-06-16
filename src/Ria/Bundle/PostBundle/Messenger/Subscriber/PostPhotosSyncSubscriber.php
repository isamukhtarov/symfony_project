<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostUpdated;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostPhotosSyncSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function __invoke(PostUpdated $message): void
    {
        $post = $this->postRepository->find($message->getPostId());
        $otherTranslationsOfPost = $this->postRepository->getOtherTranslations($post->getParent()->getId(), $post->getLanguage());
        if (($post->getLanguage() !== $this->parameterBag->get('app.locale')) || empty($otherTranslationsOfPost))
            return;

        /** @var Post $translation */
        foreach ($otherTranslationsOfPost as $translation) {
            if ($post->getPhoto()->getFilename() !== $message->getPlainPostDto()->getPhoto()) // Main photo was changed.
                $translation->setPhoto($post->getPhoto());
            $translation->sync('photoRelation', $post->getPhotoRelation());
            $this->postRepository->save($translation);
        }
    }
    
    public static function getHandledMessages(): iterable
    {
        yield PostUpdated::class => ['priority' => 90];
    }
}