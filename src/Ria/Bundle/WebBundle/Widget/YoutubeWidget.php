<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

/**
 * Class YoutubeWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property string youtubeId
 * @property array options
 */
class YoutubeWidget extends FrontendWidget
{

    public function __construct(
        Environment $twig,
        protected ParameterBagInterface $parameterBag
    )
    {
        parent::__construct($twig);
    }

    public function run(): string
    {
        return $this->render('youtube.html.twig', [
            'youtubeId' => $this->youtubeId,
            'options'   => HtmlBuilder::tagAttributes(array_merge($this->getDefaultOptions(), $this->options))
        ]);
    }

    private function getDefaultOptions(): array
    {
        return [
            'rel'            => 0,
            'color'          => 'white',
            'modestbranding' => 1,
            'hl'             => $this->parameterBag->get('app.locale')
        ];
    }

}