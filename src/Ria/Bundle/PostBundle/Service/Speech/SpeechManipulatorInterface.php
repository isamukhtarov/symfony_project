<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service\Speech;

use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface SpeechManipulatorInterface
{
    public function upload(UploadedFile $file): string;

    public function delete(string $filename): void;
}