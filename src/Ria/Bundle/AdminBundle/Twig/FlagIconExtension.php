<?php
declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Twig;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\AdminBundle\Service\FlagIconRenderer;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlagIconExtension extends AbstractExtension
{
    public function __construct(
        private Environment $twig,
        private FlagIconRenderer $flagIconRenderer
    ) {}

    public function getFunctions()
    {
        return [
            new TwigFunction('flagIcon', [$this, 'renderFlagIcon'], ['is_safe' => ['all']])
        ];
    }

    #[Pure] public function renderFlagIcon(string $locale): string
    {
        return $this->flagIconRenderer->render($locale);
    }
}