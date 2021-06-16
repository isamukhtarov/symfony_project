<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Transformers;

use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Class PostViewModelToJsonTransformer
 * @package Ria\Bundle\PostBundle\Query\Transformers
 */
class PostViewModelToJsonTransformer
{

    public function __construct(
        private PostViewModel $post,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig,
        private ImagePackage $imagePackage
    ){}

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function transform(): array
    {
        $formatDateTime = ($this->twig->getFilter('format_datetime')->getCallable());

        return array_replace($this->post->toArray(), [
            'preparedTitle'      => $this->post->getPreparedTitle(),
            'titleWithoutQuotes' => $this->post->getTitleWithoutQuotes(),
            'published_at'       => $this->post->publishedAt->format('H:i'),
            'published_at_long'  => $formatDateTime($this->twig, $this->post->publishedAt, 'none', 'none', 'd MMMM, y'),
            'url'                => $this->urlGenerator->generate('post_view',
                                        ['categorySlug' => $this->post->category_slug, 'slug' => $this->post->slug]),
            'categoryUrl'        => $this->urlGenerator->generate('category_view',
                                        ['slug' => $this->post->category_slug]),
            'imageThumb'         => $this->imagePackage->getUrl($this->post->image, ['thumb' => 180]),
            'description'        => $this->post->description
        ]);
    }

}