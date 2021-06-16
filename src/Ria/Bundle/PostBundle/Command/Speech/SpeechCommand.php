<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Speech;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Component\Speech\MP3File;

class SpeechCommand
{
    #[Assert\File(maxSize: 15 * 1024 * 1024)]
    public UploadedFile $file;

    public function getFileDurationInSeconds(string $filePathName): int
    {
        $file = new MP3File($filePathName);
        return (int)$file->getDurationEstimate();
    }
}