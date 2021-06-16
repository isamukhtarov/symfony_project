<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArchiveController extends AbstractController
{

    protected const POSTS_COUNT = 20;

    public function __construct(protected PostRepository $postRepository)
    {
    }

    #[Route("/archive/{date}/", name: "archive", requirements: ["date" => "[\d]{4}\/[\d]{2}\/[\d]{2}"], methods: ['GET'], priority: 2)]
    #[Route("/archive/{date}/{page<\d+>}/", name: "archive_with_page", requirements: ["date" => "[\d]{4}\/[\d]{2}\/[\d]{2}"], methods: ['GET'], priority: 2)]
    public function index(Request $request, string $date, ?int $page = null): Response
    {
        $dateForQuery = str_replace('/', '-', $date);

        $urlTranslations = $page
            ? $this->getUrlTranslations('archive_with_page', ['page' => $page, 'date' => $date])
            : $this->getUrlTranslations('archive', ['date' => $date]);

        $posts = $this->postRepository->getLastPosts($request->getLocale(), self::POSTS_COUNT, $page, [
            ['dateBetween' => [$dateForQuery . ' 00:00:00', $dateForQuery . ' 23:59:59']]
        ]);

        return $this->render('@RiaWeb/index/archive.html.twig', [
            'posts'           => $posts,
            'urlTranslations' => $urlTranslations,
            'date'            => $dateForQuery
        ]);
    }

    #[Route("/archive-ajax/{date}/", name: "archive_ajax", requirements: ["date" => "[\d]{4}\-[\d]{2}\-[\d]{2}"], methods: ['GET'], priority: 2)]
    public function authorAjax(Request $request, CommandValidatorInterface $validator, string $date): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->postRepository->getLastPosts($request->getLocale(), self::POSTS_COUNT, null, [
            ['dateBetween' => [$date . ' 00:00:00', $date . ' 23:59:59']],
            ['dateLess' => $timestamp],
        ]);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    protected function getUrlTranslations(string $route, array $params = []): array
    {
        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urlTranslations[$language] = $this->generateUrl($route,
                array_merge($params, ['_locale' => $language]),
                UrlGeneratorInterface::ABSOLUTE_URL);
        }
        return $urlTranslations;
    }

}