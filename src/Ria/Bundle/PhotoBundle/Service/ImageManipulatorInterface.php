<?php

namespace Ria\Bundle\PhotoBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageManipulatorInterface
{
    public function upload(UploadedFile $file): string;

    public function crop(int|float $width, int|float $height, int|float $x, int|float $y, string $filename): void;

    public function addWatermark(string $filename, string $watermarkPath): void;

    public function delete(string $filename): void;

    public function dropUniCropperCache(string $filename): void;

}