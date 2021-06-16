<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use DateTime;
use InvalidArgumentException;
use Doctrine\ORM\AbstractQuery;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\CoreBundle\Component\Sitemap\MapItem;
use Symfony\Component\Console\Output\OutputInterface;
use Ria\Bundle\CoreBundle\Component\Sitemap\IndexItem;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class SitemapGenerateCommand extends Command
{
    const ITEMS_PER_PAGE = 10000;

    private ?string $language;
    private ?string $languageForUrl;

    public function __construct(
        string $name = null,
        private CategoryRepository $categoryRepository,
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator,
        private PostRepository $postRepository,
        private ContainerInterface $container,
        private RedisClient $redisClient,
        private RouterInterface $router,
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('language', InputArgument::OPTIONAL, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $language = $input->getArgument('language');

        if (!in_array($language, $this->parameterBag->get('app.supported_locales')))
            throw new InvalidArgumentException('Invalid language given.');

        $this->language = $language;
        $this->languageForUrl = ($language == $this->parameterBag->get('app.locale')) ? null : $language;

        $static = [
            new IndexItem($this->urlGenerator->generate('sitemap_categories', [
                '_locale' => $this->languageForUrl
            ], UrlGeneratorInterface::ABSOLUTE_URL)),
        ];
        $items = $this->getPostsPages();
        $indexGenerateData = array_merge($static, $items);
        $allIndexXmlData   = $this->container
                            ->get('twig')
                            ->render("@RiaWeb/default/sitemap/partials/index.html.twig", compact('indexGenerateData'));

        $language = $this->language;
        $this->redisClient->set("sitemap-{$language}.xml", $allIndexXmlData);

        $this->generateCategories();

        $io->success("Sitemap generated\n");
        return Command::SUCCESS;
    }

    public function generateCategories(): void
    {
        $categoryItems = array_map(function (array $category) {
            return new MapItem(
                $this->urlGenerator->generate('category_view', [
                    'slug' => $category['slug'],
                    '_locale' => $this->languageForUrl
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                null,
                MapItem::HOURLY
            );
        }, $this->categoryRepository->get($this->language));

        $categoriesDataXml = $this->container
                            ->get('twig')
                            ->render("@RiaWeb/default/sitemap/partials/categories.html.twig", compact("categoryItems"));

        $language = $this->language;
        $this->redisClient->set("sitemap-categories-{$language}.xml", $categoriesDataXml);
    }

    private function getPostsPages()
    {
        $range = range(0, (int)($this->postRepository->getCount($this->language) / self::ITEMS_PER_PAGE));
        $items = [];
        foreach ($range as $i => $start) {
            $currentStart = isset($range[($i + 1)]) ? $range[($i + 1)] * self::ITEMS_PER_PAGE : 0;
            $lastPost     = $this->postRepository
                            ->getByRange($currentStart, 1, $this->language, AbstractQuery::HYDRATE_ARRAY);

            /** @var DateTime $datetime */
            $items[]  = new IndexItem(
                $this->urlGenerator->generate('sitemap_posts', [
                    'start' => $start,
                    '_locale' => $this->languageForUrl
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                $lastPost[0]['updatedAt']->getTimestamp()
            );

            $language = $this->language;
            $this->redisClient->del("sitemap-posts-{$language}-{$start}.xml");
        }

        return $items;
    }
}
