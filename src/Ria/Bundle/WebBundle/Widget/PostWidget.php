<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PostBundle\Query\Hydrator\PostHydrator;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Twig\Environment;

/**
 * Class PostWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property-read string|null $label
 */
class PostWidget extends FrontendWidget
{

    protected PostRepository $postRepository;

    public function __construct(Environment $twig, PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
        parent::__construct($twig);
    }

    public function run(): string
    {
        $label = $this->label ?? null;

        if (isset($this->filters)) {
            $posts = $this->getPosts($this->filters);
        } elseif (!empty($this->method)) {
            $posts = $this->postRepository->{$this->method['name']}($this->method['params']);
        }

        return $this->render($this->template, isset($posts) ? compact('posts', 'label') : []);
    }

    protected function getPosts(array $filters): array
    {
        return $this->postRepository->match($filters, PostHydrator::HYDRATION_MODE);
    }

}