<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PostBundle\Entity\Tag\Tag;
use Ria\Bundle\PostBundle\Entity\Tag\Translation;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class TagController
 * @package Ria\Bundle\WebBundle\Controller
 */
class TagController extends AbstractController
{

    protected const POSTS_COUNT = 20;

    public function __construct(
        protected TagRepository $tagRepository,
        protected PostRepository $postRepository
    )
    {
    }

    #[Route("/tag/{slug}/", name: "tag_view", methods: ['GET'], priority: 2)]
    public function view(Request $request, string $slug)
    {
        $tag = $this->tagRepository->getBySlug($slug, $request->getLocale());

        if (!$tag) {
            throw $this->createNotFoundException();
        }

        $posts           = $this->postRepository->getByTag($tag->id, $request->getLocale(), self::POSTS_COUNT);
        $urlTranslations = $this->getUrlTranslations($tag->id);

        return $this->render('@RiaWeb/index/tag.html.twig',
            compact('tag', 'posts', 'urlTranslations'));
    }

    #[Route("/tag-ajax/{tagId<\d+>}/", name: "tag_ajax", methods: ['GET'], priority: 2)]
    public function tagAjax(Request $request, CommandValidatorInterface $validator, int $tagId): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->postRepository->getByTag($tagId, $request->getLocale(), self::POSTS_COUNT, $timestamp);

        return new JsonResponse([
            'html' => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    private function getUrlTranslations(int $tagId): array
    {
        /** @var Tag $tag */
        $tag = $this->tagRepository->find($tagId);

        $availableUrls = [];
        /** @var Translation $translation */
        foreach ($tag->getTranslations() as $translation) {
            $availableUrls[$translation->getLanguage()] = $this->generateUrl('tag_view',
                ['slug' => $tag->getSlug(), '_locale' => $translation->getLanguage()],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urlTranslations[$language] = $availableUrls[$language] ?? null;
        }

        return $urlTranslations;
    }

}