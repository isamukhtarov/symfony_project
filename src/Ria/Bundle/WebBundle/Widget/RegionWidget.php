<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Doctrine\ORM\Query\Expr\Join;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PostBundle\Query\Hydrator\RegionHydrator;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\PostBundle\Query\ViewModel\RegionViewModel;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Class RegionWidget
 * @package Ria\Bundle\WebBundle\Widget
 */
class RegionWidget extends FrontendWidget
{

    protected Environment $twig;
    protected RequestStack $requestStack;
    protected RegionRepository $regionRepository;
    protected PostRepository $postRepository;

    #[Pure] public function __construct(
        Environment $twig,
        RequestStack $requestStack,
        RegionRepository $regionRepository,
        PostRepository $postRepository
    )
    {
        $this->requestStack     = $requestStack;
        $this->regionRepository = $regionRepository;
        $this->postRepository   = $postRepository;

        parent::__construct($twig);
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(): string
    {
        $regions = $this->getRegions();
        $posts   = $this->getPosts();

        if (empty($regions)) {
            return '';
        }

        return $this->render($this->template, compact('regions', 'posts'));
    }

    /**
     * @return RegionViewModel[]
     */
    protected function getRegions(): array
    {
        return $this->regionRepository->createQueryBuilder('r')
            ->select('r.id', 'rt.title', 'rt.slug')
            ->join('r.translations', 'rt', Join::WITH, 'rt.language = :language')
            ->getQuery()
            ->execute([
                'language' => $this->requestStack->getCurrentRequest()->getLocale(),
            ], RegionHydrator::HYDRATION_MODE);
    }

    /**
     * @return PostViewModel[]
     */
    protected function getPosts(): array
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->postRepository->match([
            ['select' => ['id', 'type.type', 'title', 'slug', 'publishedAt']],
            'latest',
            'published',
            'withCategory',
            'withPhoto',
            ['withCity' => ['language' => $language]],
            ['lang' => $language],
            ['limit' => 6]
        ]);
    }

}