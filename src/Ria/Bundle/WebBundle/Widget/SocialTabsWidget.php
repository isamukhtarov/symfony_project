<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;

/**
 * Class SocialTabsWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property-read string language
 * @property-read string theme
 */
class SocialTabsWidget extends FrontendWidget
{

    private array $tabs = [
        'ge' => ['facebook', 'twitter', 'instagram'],
        'ru' => ['facebook', 'twitter', 'instagram'],
        'en' => ['facebook', 'twitter', 'instagram'],
    ];

    public function run(): string
    {
        $tabs     = $this->tabs[$this->language];
        $count    = count($tabs) - 1;
        $template = $tabs[rand(0, $count)];

        return $this->render($template . '.html.twig', ['language' => $this->language]);
    }

}