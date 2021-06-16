<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form;

use Symfony\Component\Form\FormInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Form\Type\Post\AbstractPostType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PostRedirectPathGuesser
{
    public function __construct(
        private PostRepository $postRepository,
        private UrlGeneratorInterface $urlGenerator,
    ){}

    public function guess(FormInterface $form, ?Post $post = null): string
    {
        if ($form->get(AbstractPostType::SUBMIT_AND_STAY)->isClicked()) {
            $item = $post ?: $this->postRepository->findOneBy([], ['id' => 'DESC']);
            return $this->urlGenerator->generate('posts.update', ['id' => $item->getId()]);
        }

        return $this->urlGenerator->generate('posts.index', ['filter_p.language' => $form->getViewData()->language]);
    }
}