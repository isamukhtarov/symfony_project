<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\CurrencyBundle\Repository\CurrencyRepository;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Helper\CategoryHelper;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Query\Specifications\Post\Range;
use Ria\Bundle\PostBundle\Query\Transformers\PostViewModelToJsonTransformer;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\WeatherBundle\Components\RegionList;
use Ria\Bundle\WeatherBundle\Repository\WeatherRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class IndexController extends AbstractController
{
    protected const FEED_POSTS_COUNT = 20;

    #[Route("/", name: "index", methods: ['GET'], priority: 2)]
    public function index(): Response
    {
        $urlTranslations = $this->getUrlTranslations('index');

        return $this->render('@RiaWeb/index/index.html.twig', compact('urlTranslations'));
    }

    #[Route(
        "/latest-news/{range<today|yesterday|this_week|this_month|last_week|last_month>}/",
        name: "feed_with_range",
        methods: ['GET'], priority: 2
    )]
    #[Route(
        "/latest-news/{page<\d+>}/",
        name: "feed_with_page",
        methods: ['GET'], priority: 2
    )]
    #[Route(
        "/latest-news/",
        name: "feed",
        methods: ['GET'], priority: 2
    )]
    public function feed(Request $request, PostRepository $postRepository, ?int $page = 1, ?string $range = null): Response
    {
        $urlTranslations = $range
            ? $this->getUrlTranslations('feed_with_range', ['range' => $range])
            : $this->getUrlTranslations('feed');

        $filters = ['hasAudio'];
        if ($range) {
            $filters[] = ['range' => $range];
        }

        $ranges = Range::getRanges();
        $posts  = $postRepository->getLastPosts($request->getLocale(), self::FEED_POSTS_COUNT, $page, $filters);

        return $this->render('@RiaWeb/index/feed.html.twig', compact(
            'urlTranslations',
            'posts',
            'ranges'
        ));
    }

    #[Route("/feed-ajax/", name: "feed_ajax", methods: ['GET'], priority: 2)]
    public function feedAjax(Request $request, CommandValidatorInterface $validator, PostRepository $postRepository): Response
    {
        $timestamp = $request->get('timestamp');
        $range     = $request->get('range');

        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $filters = [
            'hasAudio',
            ['dateLess' => $timestamp]
        ];
        if (!empty($range) && in_array($range, Range::getRanges())) {
            $filters[] = ['range' => $range];
        }

        $posts = $postRepository->getLastPosts($request->getLocale(), self::FEED_POSTS_COUNT, null, $filters);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_feed-post-item.html.twig', compact('posts')),
            'lastDate'     => date('d.m.Y', strtotime($timestamp)),
            'limitReached' => count($posts) < self::FEED_POSTS_COUNT
        ]);
    }

    #[Route("/weather/{city}/", name: "weather_with_city", methods: ['GET'], priority: 2)]
    #[Route("/weather/", name: "weather", methods: ['GET'], priority: 2)]
    public function weather(
        Request $request,
        WeatherRepository $repository,
        TranslatorInterface $translator,
        ParameterBagInterface $parameterBag,
        string $city = 'tbilisi'
    ): Response
    {
        $regions = new RegionList($parameterBag->get('regions'));
        if (!in_array($city, $regions->getOnlyNames())) {
            throw $this->createNotFoundException();
        }

        $record = $repository->getLastOne();
        if ($record) {
            $record['data'] = json_decode($record['data'], true);
        }

        $urlTranslations = $request->get('city')
            ? $this->getUrlTranslations('weather_with_city', ['city' => $city])
            : $this->getUrlTranslations('weather');

        if (empty($record['data'][$city][date('Y-m-d')])) {
            throw $this->createNotFoundException($translator->trans('weather_for_region_no_found', [], 'weather'));
        }

        return $this->render('@RiaWeb/index/weather.html.twig', [
            'urlTranslations' => $urlTranslations,
            'city'            => $city,
            'cityLabel'       => $translator->trans($city, [], 'weather'),
            'weather'         => $record['data'][$city],
            'regionsList'     => $regions->getOnlyNames(),
        ]);
    }

    #[Route("/currency/", name: "currency", methods: ['GET'], priority: 2)]
    public function currency(
        Request $request,
        CurrencyRepository $currencyRepository,
        CategoryRepository $categoryRepository
    ): Response
    {
        $subCategories = $categoryRepository->getSubCategories(4, $request->getLocale());

        return $this->render('@RiaWeb/index/currency.html.twig', [
            'items'                  => $currencyRepository->lastByDate($currencyRepository->getLastDate()->format('Y-m-d')),
            'economicsCategoriesIds' => CategoryHelper::pluckChildren(4, $subCategories),
            'urlTranslations'        => $this->getUrlTranslations('currency')
        ]);
    }

    #[Route("/about/", name: "about", methods: ['GET'], priority: 2)]
    public function about(): Response
    {
        return $this->render('@RiaWeb/index/about.html.twig', [
            'urlTranslations' => $this->getUrlTranslations('about')
        ]);
    }

    #[Route("/contacts/", name: "contact", methods: ['GET'], priority: 2)]
    public function contact(): Response
    {
        return $this->render('@RiaWeb/index/contact.html.twig', [
            'urlTranslations' => $this->getUrlTranslations('contact')
        ]);
    }

    #[Route("/vacancies/", name: "vacancies", methods: ['GET'], priority: 2)]
    public function vacancies(): Response
    {
        return $this->render('@RiaWeb/index/vacancies.html.twig', [
            'urlTranslations' => $this->getUrlTranslations('vacancies')
        ]);
    }

    #[Route("/set-language/", name: "setLanguage", methods: ['POST'], priority: 2)]
    public function setLanguage(Request $request): Response
    {
        $language         = $request->get('language');
        $supportedLocales = $this->getParameter('app.supported_locales');

        if (!in_array($language, $supportedLocales)) {
            throw $this->createNotFoundException();
        }

        $cookie = new Cookie(
            name: 'priority-lang',
            value: $language,
            expire: new \DateTime('+1 week'),
            path: '/',
            domain: null,
            secure: false,
            httpOnly: false,
        );

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        return $response;
    }

    #[Route("/news-feed/", name: "newsFeed", methods: ['GET'], priority: 2)]
    public function newsFeed(
        Request $request,
        PostRepository $postRepository,
        UrlGeneratorInterface $urlGenerator,
        ImagePackage $imagePackage
    ): Response
    {
        /** @var Environment $twig */
        $twig = $this->get('twig');

        $posts = $postRepository->getLastPosts($request->getLocale(), self::FEED_POSTS_COUNT, null, ['hasAudio']);
        $data  = array_map(function (PostViewModel $post) use ($urlGenerator, $twig, $imagePackage) {
            return (new PostViewModelToJsonTransformer($post, $urlGenerator, $twig, $imagePackage))->transform();
        }, $posts);

        return new JsonResponse(['posts' => $data]);
    }

    private function getUrlTranslations(string $route, array $params = []): array
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