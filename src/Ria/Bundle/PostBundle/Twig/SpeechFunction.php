<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Twig;

use Ria\Bundle\PostBundle\Service\Speech\SpeechPackage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SpeechFunction extends AbstractExtension
{
    public function __construct(
        private SpeechPackage $package
    ){}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('speech', [$this, 'getSpeech'], ['is_safe' => ['all']]),
        ];
    }

    public function getSpeech(string $path): string
    {
        return $this->package->getUrl($path);
    }
}