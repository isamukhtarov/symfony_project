<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


/**
 * Class ExtractPhotos
 * @package Ria\News\Core\Pipes
 */
class ExtractPhotos
{

    private string $language;

    public function __construct(
        protected PhotoRepository $photoRepository,
        protected ImagePackage $imagePackage,
        protected ParameterBagInterface $parameterBag,
        string $language = null
    )
    {
        $this->language = $language ?? $this->parameterBag->get('app.locale');
    }

    public function handle(string $content, Closure $next): string
    {
        preg_match_all('/{{photo-(big|small-left|small-right)-([a-z0-9\-]+)}}/i', $content, $matches);

        foreach ($matches[2] as $index => $photoId) {
            $photo = $this->getPhoto($photoId);

            if (empty($photo)) {
                $content = str_replace($matches[0][$index], '', $content);
                continue;
            }

            $photoLink        = $this->imagePackage->getUrl($photo->getFilename());
            $photoInformation = $photo->getTranslation($this->language)->getInformation();
            $photoAuthor      = $photo->getTranslation($this->language)->getAuthor();

            if (!empty($photoInformation) && !empty($photoAuthor))
                $photoInformation = "$photoInformation / $photoAuthor";
            elseif (!empty($photoAuthor) && empty($photoInformation))
                $photoInformation = $photoAuthor;

            $photoWrapper = HtmlBuilder::tag('div',
                HtmlBuilder::tag('div',
                        HtmlBuilder::tag('img', '', ['src' => $photoLink, 'alt' => $photoInformation], false),
                    ['class' => 'image'],
                )
                . HtmlBuilder::tag('span', $photoInformation, ['class' => 'description']),
                ['class' => 'news-image']
            );

            $content = str_replace($matches[0][$index], $photoWrapper, $content);
        }

        return $next($content);
    }

    private function getPhoto(string $idOrHash): ?Photo
    {
        return is_numeric($idOrHash)
            ? $this->photoRepository->find($idOrHash)
            : $this->photoRepository->findOneBy(['filename' => $idOrHash . '.jpg']);
    }

}