<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service\Speech;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SpeechPackage
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ){}

    public function getUrl(string $path): string
    {
        $speechInfo = pathinfo($path);
        return $this->parameterBag->get('domain.static') . '/speech/' . $speechInfo['filename'] . '.mp3';
    }
}