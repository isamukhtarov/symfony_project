<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\Pipeline\PipelineInterface;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Enum\PostPreview;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Ria\Bundle\PostBundle\Entity\Post\ShortLink;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;

class PostController extends AbstractController
{
    public function __construct(
        protected PostRepository $postRepository
    )
    {
    }

    #[Route("/{categorySlug}/{slug}/", name: "post_view", methods: ['GET'])]
    public function view(Request $request, PipelineInterface $pipeline, string $categorySlug, string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug, $request->getLocale());

        if (empty($post) || !$post->isPublished() || $post->isPrivate()) {
            throw $this->createNotFoundException();
        }

        if ($post->category_slug != $categorySlug) {
            return $this->redirectToRoute('post_view', ['categorySlug' => $post->category_slug, 'slug' => $slug],
                $this->getParameter('kernel.environment') == 'dev' ? 302 : 301);
        }

        $previousPost         = $this->postRepository->getPrevious((int)$post->category_id, $post->publishedAt, $post->language);
        $translations         = $this->postRepository->getOtherTranslates((int)$post->parent, $post->language);
        $urlTranslations      = $this->getUrlTranslations(array_merge([$post], $translations));
        $shortLink            = $this->getShortLink($post->id);
        $preparedPhotoContent = $this->getPreparedPhotoContent($post);

        return $this->render('@RiaWeb/post/post-view.html.twig', compact(
            'post',
            'previousPost',
            'preparedPhotoContent',
            'urlTranslations',
            'translations',
            'shortLink',
            'pipeline'
        ));
    }

    #[Route("/amp/{categorySlug}/{slug}/", name: "post_amp", methods: ['GET'])]
    public function amp(Request $request, PipelineInterface $pipeline, PhotoRepository $photoRepository, string $categorySlug, string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug, $request->getLocale());

        if (empty($post) || !$post->isPublished() || $post->isPrivate()) {
            throw $this->createNotFoundException();
        }

        if ($post->category_slug != $categorySlug) {
            return $this->redirectToRoute('post_view', ['categorySlug' => $post->category_slug, 'slug' => $slug],
                $this->getParameter('kernel.environment') == 'dev' ? 302 : 301);
        }

        $photos = $photoRepository->getGallery($post->language, $post->id, (int)$post->photoId);

        return $this->render('@RiaWeb/../mobile/post/post-amp.html.twig', compact(
            'post',
            'photos',
            'pipeline'
        ));
    }

    #[Route("/private/{categorySlug}/{slug}/", name: "post_private", methods: ['GET'])]
    public function private(Request $request, PipelineInterface $pipeline, ParameterBagInterface $parameterBag, string $categorySlug, string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug, $request->getLocale());
        if (empty($post) || !$post->isPrivate()) {
            throw $this->createNotFoundException();
        }

        if ($post->category_slug != $categorySlug) {
            return $this->redirectToRoute('post_private', ['categorySlug' => $post->category_slug, 'slug' => $slug],
                $this->getParameter('kernel.environment') == 'dev' ? 302 : 301);
        }

        $previousPost          = $this->postRepository->getPrevious((int)$post->category_id, $post->publishedAt, $post->language);
        $translations          = $this->postRepository->getOtherTranslates((int)$post->parent, $post->language);
        $urlTranslations       = $this->getUrlTranslations(array_merge([$post], $translations));
        $shortLink             = $this->getShortLink($post->id);
        $preparedPhotoContent  = $this->getPreparedPhotoContent($post);
        $enabledAnalyticsCodes = false;

        return $this->render('@RiaWeb/post/post-view.html.twig', compact(
            'post',
            'previousPost',
            'preparedPhotoContent',
            'urlTranslations',
            'translations',
            'shortLink',
            'pipeline',
            'enabledAnalyticsCodes'
        ));
    }

    #[Route("/future/{categorySlug}/{slug}/", name: "post_future", methods: ['GET'])]
    public function future(Request $request, PipelineInterface $pipeline, string $categorySlug, string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug, $request->getLocale());
        if (empty($post) || $post->isPublished() || (!$post->isFuture()) && !$post->isActive()) {
            throw $this->createNotFoundException();
        }

        if ($post->category_slug != $categorySlug) {
            return $this->redirectToRoute('post_future', ['categorySlug' => $post->category_slug, 'slug' => $slug],
                $this->getParameter('kernel.environment') == 'dev' ? 302 : 301);
        }

        $previousPost          = $this->postRepository->getPrevious((int)$post->category_id, $post->publishedAt, $post->language);
        $translations          = $this->postRepository->getOtherTranslates((int)$post->parent, $post->language);
        $urlTranslations       = $this->getUrlTranslations(array_merge([$post], $translations));
        $shortLink             = $this->getShortLink($post->id);
        $preparedPhotoContent  = $this->getPreparedPhotoContent($post);
        $enabledAnalyticsCodes = false;

        return $this->render('@RiaWeb/post/post-view.html.twig', compact(
            'post',
            'previousPost',
            'preparedPhotoContent',
            'urlTranslations',
            'translations',
            'shortLink',
            'pipeline',
            'enabledAnalyticsCodes'
        ));
    }

    #[Route("/preview/{categorySlug}/{slug}/", name: "post.preview", methods: ['GET'])]
    public function preview(Request $request, PipelineInterface $pipeline, RedisClient $client): Response
    {
        $key = PostPreview::getCacheKey($request->get('key'));

        if (!(bool)$client->exists($key)) {
            $this->createNotFoundException('Preview not found');
        }

        $post = unserialize($client->get($key));

        return $this->render('@RiaWeb/post/post-view.html.twig', [
            'post'                 => $post,
            'previousPost'         => null,
            'preparedPhotoContent' => $this->getPreparedPhotoContent($post),
            'urlTranslations'      => [],
            'translations'         => [],
            'shortLink'            => null,
            'pipeline'             => $pipeline
        ]);
    }

    public function getShortLink(int $postId): ?string
    {
        $shortLink = $this->getDoctrine()->getManager()
            ->getRepository(ShortLink::class)
            ->findOneBy(['post' => $postId]);

        if (empty($shortLink)) {
            return null;
        }

        // TODO create param "shortLinkHost" for different environments
        return 'http://ria.az/' . $shortLink->getHash() . '/';
    }

    private function getPreparedPhotoContent(PostViewModel $post): string
    {
        if (empty($post->image)) {
            return '';
        }

        $photo = $this->getDoctrine()->getManager()
            ->getRepository(Photo::class)
            ->getPhotoInfo($post->image, $post->language);

        $photoInformation = '';

        if (!empty($photo)) {
            if (!empty($photo->information) && !empty($photo->author))
                $photoInformation = $photo->information . ' / ' . $photo->author;
            elseif (!empty($photo->author) && empty($photo->information))
                $photoInformation = $photo->author;
        }

        return $photoInformation;
    }

    private function getUrlTranslations(array $translations): array
    {
        $availableUrls = [];
        foreach ($translations as $translation) {
            $availableUrls[$translation->language] = $this->generateUrl('post_view',
                ['categorySlug' => $translation->category_slug, 'slug' => $translation->slug, '_locale' => $translation->language],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urlTranslations[$language] = $availableUrls[$language] ?? null;
        }

        return $urlTranslations;
    }

}