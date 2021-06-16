<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StoryController
 * @package Ria\Bundle\WebBundle\Controller
 */
class StoryController extends AbstractController
{

    protected const POSTS_COUNT = 19;

    public function __construct(
        protected StoryRepository $storyRepository,
        protected PostRepository $postRepository
    )
    {
    }

    #[Route("/story/{slug}/", name: "story_view", methods: ['GET'], priority: 2)]
    public function view(Request $request, string $slug): Response
    {
        $story = $this->storyRepository->findBySlug($slug, $request->getLocale());

        if (!$story) {
            throw $this->createNotFoundException();
        }

        $story->setPosts($this->getPostsByStory($story->id, $story->language));

        $urlTranslations = $this->getUrlTranslations($story->id);

        return $this->render('@RiaWeb/index/story.html.twig', compact('story', 'urlTranslations'));
    }

    #[Route("/story-ajax/{storyId<\d+>}/", name: "story_ajax", methods: ['GET'], priority: 2)]
    public function storyAjax(Request $request, CommandValidatorInterface $validator, int $storyId): JsonResponse
    {
        $timestamp = $request->get('timestamp') ?? '';
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->getPostsByStory($storyId, $request->getLocale(), $timestamp);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    protected function getPostsByStory(int $storyId, string $language, ?string $timestamp = null): array
    {
        return $this->postRepository->getLastPosts($language, self::POSTS_COUNT, null, [
            ['story' => $storyId],
            ['limit' => self::POSTS_COUNT],
            $timestamp ? ['dateLess' => $timestamp] : null
        ]);
    }

    protected function getUrlTranslations(int $storyId): array
    {
        $model = $this->storyRepository->find($storyId);

        $urls = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $translation     = $model->getTranslation($language);
            $urls[$language] = $this->generateUrl(
                'story_view',
                ['slug' => $translation->getSlug(), '_locale' => $translation->getLanguage()],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $urls;
    }

}