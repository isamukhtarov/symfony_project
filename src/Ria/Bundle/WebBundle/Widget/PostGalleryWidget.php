<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

/**
 * Class PostGalleryWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property int postId
 * @property int|null mainPhotoId
 */
class PostGalleryWidget extends FrontendWidget
{

    public function __construct(
        Environment $twig,
        protected PhotoRepository $repository,
        protected ParameterBagInterface $parameterBag
    )
    {
        parent::__construct($twig);
    }

    public function run(): string
    {
        return $this->render('post-gallery.html.twig', ['photos' => $this->repository->getGallery(
            $this->parameterBag->get('app.locale'),
            $this->postId,
            (int)$this->mainPhotoId
        )]);
    }

}