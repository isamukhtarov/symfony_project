<?php

namespace Ria\Bundle\CoreBundle\Twig;

use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private array $scriptsStack = [];
    private array $stylesStack = [];

    public function __construct(
        private Packages $packages,
        private LoggerInterface $logger
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('registerScripts', [$this, 'registerScripts'], ['is_safe' => ['all']]),
            new TwigFunction('importScripts', [$this, 'importScripts'], ['is_safe' => ['all']]),
            new TwigFunction('registerStyles', [$this, 'registerStyles'], ['is_safe' => ['all']]),
            new TwigFunction('importStyles', [$this, 'importStyles'], ['is_safe' => ['all']]),
        ];
    }

    public function registerScripts(...$scripts): void
    {
        $this->scriptsStack = array_merge($this->scriptsStack, $scripts);
    }

    #[Pure] public function importScripts(): string
    {
        $scriptsStack = $this->scriptsStack;
        $this->scriptsStack = [];

        return implode("\n", array_map(
            fn($script) => sprintf("<script src='%s'></script>", $this->packages->getUrl($script)),
            $scriptsStack
        ));
    }

    public function registerStyles(...$styles): void
    {
        $this->stylesStack = array_merge($this->stylesStack, $styles);
    }

    #[Pure] public function importStyles(): string
    {
        $stylesStack = $this->stylesStack;
        $this->stylesStack = [];

        return implode("\n", array_map(
            fn($style) => sprintf('<link href="%s" rel="stylesheet">', $this->packages->getUrl($style)),
            $stylesStack
        ));
    }
}