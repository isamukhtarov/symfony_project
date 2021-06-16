<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service\Archive;

use JetBrains\PhpStorm\Pure;

class ArchiveDto
{
    public function __construct(
        private string $filename,
        private string $pathToArchive,
    ){}

    public function getStream(): ?string
    {
        return file_get_contents($this->pathToArchive);
    }

    #[Pure] public function getFileSize(): int
    {
        return filesize($this->pathToArchive);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        return $this->pathToArchive;
    }
}