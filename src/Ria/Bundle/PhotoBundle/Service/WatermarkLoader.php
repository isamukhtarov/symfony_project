<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Service;

use Ria\Bundle\PhotoBundle\Entity\Photo;

class WatermarkLoader
{
    private const DARK_IMAGE = 'report_logo_black.png';
    private const WHITE_IMAGE = 'report_logo_white.png';

    private Photo $photo;

    public function __construct(
        private string $watermarksPath,
        private ImageManipulatorInterface $imageManipulator,
    ) {}

    public function process(Photo $photo)
    {
        ini_set('memory_limit', '500M');
        $this->photo = $photo;

        $this->imageManipulator->addWatermark($photo->getFilename(), $this->getWatermark());
    }

    private function getWatermark(): string
    {
        $gradientRgb = explode(',', $this->photo->getGradientRgb());

        return $this->isImageDark($gradientRgb)
            ? $this->watermarksPath . DIRECTORY_SEPARATOR . self::WHITE_IMAGE
            : $this->watermarksPath . DIRECTORY_SEPARATOR . self::DARK_IMAGE;
    }

    private function isImageDark(array $rgb): bool
    {
        $darkness = ($rgb[0] * 0.299) + ($rgb[1] * 0.587) + ($rgb[2] * 0.114);
        return $darkness <= 186;
    }

}