<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImagePackage
{
    public function __construct(
        private string $defaultImage,
        private KernelInterface $kernel,
        private ParameterBagInterface $parameterBag,
    ) {}

    public function getUrl(?string $path = null, array $params = null): string
    {
        if (!$path) {
            $path = $this->defaultImage;
        }

        $imageInfo = pathinfo($path);
        $resultUrl = $this->parameterBag->get('domain.static') . '/photo/' . $imageInfo['filename'];

        if ($params && isset($params['thumb'])) {
            $resultUrl .= '_' . $params['thumb'];
        }

        return $resultUrl . '.' . $imageInfo['extension'];
    }

    public function getAbsolutePath(string $filename): string
    {
        return $this->kernel->getProjectDir() . '/uploads/photo/' . $filename;
    }
}