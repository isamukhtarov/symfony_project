<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Twig;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetFunction extends AbstractExtension
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('widget', [$this, 'renderWidget'], ['is_safe' => ['all']])
        ];
    }

    public function renderWidget(string $widgetName, array $options = []): string
    {
        /** @var FrontendWidget $widget */
        $widget = $this->container->get($widgetName);
        $widget
            ->setOptions($options)
            ->setAlias($widgetName);

        return $widget->run();
    }

}