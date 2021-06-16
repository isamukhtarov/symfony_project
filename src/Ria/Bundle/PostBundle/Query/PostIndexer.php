<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query;

use stdClass;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostIndexer
{
    private array $config;

    public function __construct(
        private Client $client,
        private LoggerInterface $logger,
        private ParameterBagInterface $parameterBag,
    )
    {
        $this->setConfig($this->parameterBag->get('app.elastic_search'));
    }

    public function index(Post $post): void
    {
        $postCategory = $post->getCategory()->getTranslation($post->getLanguage());

        $this->client->index([
            'index' => $this->config['index'],
            'type'  => $this->config['type'],
            'id'    => $post->getId(),
            'body'  => [
                'id'             => $post->getId(),
                'parent_id'      => $post->getParent()->getId(),
                'title'          => $post->getTitle(),
                'description'    => strip_tags((string)$post->getDescription()),
                'slug'           => $post->getSlug(),
                'content'        => strip_tags((string)$post->getContent()),
                'category_title' => $postCategory->getTitle(),
                'category_slug'  => $postCategory->getSlug(),
                'lang'           => $post->getLanguage(),
                'image'          => $post->getPhoto()?->getFilename(),
                'type'           => $post->getType(),
                'status'         => $post->getStatus(),
                'icon'           => $post->getIcon(),
                'published_at'   => $post->getPublishedAt()->format('Y-m-d H:i:s'),
                'views'          => $post->getViews(),
            ]
        ]);
    }

    public function clear(): void
    {
        $this->client->deleteByQuery([
            'index' => $this->config['index'],
            'type'  => $this->config['type'],
            'body'  => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
            ],
        ]);
    }

    public function remove($post): void
    {
        $this->client->delete([
            'index' => $this->config['index'],
            'type'  => $this->config['type'],
            'id'    => ($post instanceof Post) ? $post->getId() : $post->id,
        ]);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function setConfig(array $params = []): void
    {
        if (!isset($params['type']) || !isset($params['index']))
            throw new InvalidArgumentException("Key [type] or [index] is missing from ES configuration.");
        $this->config = $params;
    }
}