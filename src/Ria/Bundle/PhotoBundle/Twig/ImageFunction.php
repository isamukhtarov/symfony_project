<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Twig;

use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageFunction extends AbstractExtension
{
    public function __construct(
        private ImagePackage $package
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('image', [$this, 'getImage'], ['is_safe' => ['all']]),
        ];
    }

    public function getImage(?string $path = '', array $params = null): string
    {
        return $this->package->getUrl($path, $params);
    }

}