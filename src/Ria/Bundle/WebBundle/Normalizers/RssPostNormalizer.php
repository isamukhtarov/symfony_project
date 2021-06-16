<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Normalizers;

use Ria\Bundle\CoreBundle\Helper\StringHelper;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

abstract class RssPostNormalizer implements ContextAwareNormalizerInterface
{

    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected ImagePackage $imagePackage
    )
    {
    }

    /**
     * @param PostViewModel $post
     * @return array
     */
    abstract protected function getAdditionalFields(PostViewModel $post): array;

    /**
     * @param PostViewModel[] $posts
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize($posts, string $format = null, array $context = [])
    {
        return collect($posts)
            ->transform(function (PostViewModel $post) {
                $record = [
                    'id'             => $post->id,
                    'title'          => $post->title,
                    'absoluteUrl'    => $this->urlGenerator->generate('post_view',
                        ['categorySlug' => $post->category_slug, 'slug' => $post->slug],
                        UrlGeneratorInterface::ABSOLUTE_URL),
                    'description'    => $post->description,
                    'rssDate'        => $post->publishedAt->format('r'),
                    'image'          => $post->hasImage() ? $this->imagePackage->getUrl($post->image, ['thumb' => 490]) : null,
                    'category_id'    => $post->category_id,
                    'category_title' => StringHelper::mbUcFirst($post->category_title),
                    'author_name'    => $post->getAuthorName(),
                ];

                return array_replace($record, $this->getAdditionalFields($post));
            })->toArray();
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof PostViewModel;
    }

}