<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\WebBundle\Normalizers\RssMainNormalizer;
use Ria\Bundle\WebBundle\Normalizers\RssMediametricsNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RssController extends AbstractController
{

    public function __construct(
        protected PostRepository $repository,
        protected UrlGeneratorInterface $urlGenerator,
        protected ImagePackage $imagePackage,
    )
    {
    }

    #[Route("/rss/", name: "rss_main", defaults: ["_format" => "xml"], methods: ['GET'], priority: 2)]
    public function main(Request $request): Response
    {
        $posts = (new RssMainNormalizer($this->urlGenerator, $this->imagePackage))
            ->normalize($this->getPosts($request->getLocale()));

        return $this->render('@RiaWeb/rss/main.html.twig', compact('posts'));
    }

    #[Route("/rss/mediametrics/", name: "rss_mediametrics", defaults: ["_format" => "xml"], methods: ['GET'], priority: 2)]
    public function mediametrics(Request $request): Response
    {
        $records = $this->getPosts($request->getLocale(), [
            ['addSelect' => ['content']],
            ['limit' => 50]
        ]);

        $posts = (new RssMediametricsNormalizer($this->urlGenerator, $this->imagePackage))->normalize($records);

        return $this->render('@RiaWeb/rss/mediametrics.html.twig', compact('posts'));
    }

    protected function getPosts(string $language, array $filters = []): array
    {
        $specifications = [
            ['select' => [
                'id', 'type.type', 'title', 'language', 'description', 'slug', 'publishedAt'
            ]],
            'latest',
            'published',
            ['lang' => $language],
            ['limit' => 100],
            'withPhoto'
        ];

        if (empty($filters['withCategory'])) {
            $specifications[] = ['withCategory' => $language];
        }

        if (empty($filters['withAuthor'])) {
            $specifications[] = ['withAuthor' => ['language' => $language]];
        }

        if (!empty($filters['addSelect'])) {
            $specifications['select'] = array_merge($specifications['select'], $filters['addSelect']);
            unset($filters['addSelect']);
        }

        return $this->repository->match(array_merge($specifications, $filters));
    }

}