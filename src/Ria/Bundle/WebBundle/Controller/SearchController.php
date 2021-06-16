<?php

declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use DateTime;
use Elasticsearch\Client;
use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    public function __construct(protected Client $client)
    {
    }

    #[Route("/search/", name: "search", methods: ['GET'], priority: 2)]
    public function index(Request $request): Response
    {
        $query = $request->get('query', '');

        if (empty($clearedQuery = $this->clearQuery($query))) {
            throw $this->createNotFoundException();
        }
        if ($query != $clearedQuery) {
            return $this->redirectToRoute('search', ['query' => $clearedQuery]);
        }

        return $this->render('@RiaWeb/index/search.html.twig', [
            'query'           => $clearedQuery,
            'queryLabel'      => mb_convert_case($clearedQuery, MB_CASE_TITLE),
            'posts'           => $this->getPosts($clearedQuery, $request->getLocale()),
            'urlTranslations' => $this->getUrlTranslations(['query' => $clearedQuery])
        ]);
    }

    #[Route("/search-ajax/", name: "search_ajax", methods: ['GET'], priority: 2)]
    public function searchAjax(Request $request, CommandValidatorInterface $validator): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->getPosts($request->get('query'), $request->getLocale(), $timestamp);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_search-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < 10
        ]);

    }

    protected function getPosts(string $query, string $language, ?string $timestamp = null): array
    {
        $must   = [];
        $must[] = [
            'multi_match' => [
                'query'       => $query,
                'type'        => 'phrase_prefix',
                'fields'      => [
                    'title',
                    'content'
                ],
                'tie_breaker' => 0.3,
            ],
        ];

        if (!empty($timestamp)) {
            $must[] = [
                'range' => [
                    'published_at' => ['lt' => $timestamp],
                ],
            ];
        }

        $response = $this->client->search([
            'index' => 'reportge',
            'type'  => 'posts',
            'body'  => [
                'from'      => 0,
                'size'      => 10,
                'sort'      => ['published_at' => ['order' => 'desc']],
                'query'     => [
                    'bool' => [
                        'must'   => $must,
                        'filter' => [
                            'term' => ['lang' => $language],
                        ],
                    ],
                ],
                'highlight' => [
                    'fields'              => [
                        'title'   => new \stdClass(),
                        'content' => new \stdClass(),
                    ],
                    'fragment_size'       => 300,
                    'number_of_fragments' => 1,
                    'post_tags'           => ['</span>'],
                    'pre_tags'            => ['<span class="highlight">'],
                    'require_field_match' => false,
                ],
            ],
        ]);

        $posts = [];
        foreach ($response['hits']['hits'] as $hit) {
            $post              = new PostViewModel($hit['_source']);
            $post->publishedAt = new DateTime($hit['_source']['published_at']);

            if (isset($hit['highlight']['title'])) {
                $post->title = $hit['highlight']['title'][0];
            }

            if (isset($hit['highlight']['content'])) {
                $post->description = $hit['highlight']['content'][0];
            }

            $posts[] = $post;
        }

        return $posts;
    }

    protected function clearQuery(string $query): string
    {
        $string = preg_replace('/[^\p{L}\d]/u', ' ', $query);
        return mb_strtolower(preg_replace('/(\s{1})\1*/ui', ' ', trim($string)), 'utf-8');
    }

    private function getUrlTranslations(array $params = []): array
    {
        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urlTranslations[$language] = $this->generateUrl('search',
                array_merge($params, ['_locale' => $language]),
                UrlGeneratorInterface::ABSOLUTE_URL);
        }
        return $urlTranslations;
    }


}