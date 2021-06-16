<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service\Speech;

use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class SpeechManipulator implements SpeechManipulatorInterface
{
    public function __construct(
        private string $uploadDirectory,
        private SluggerInterface $slugger
    ){}

    public function upload(UploadedFile $file): string
    {
        $safeFileName = $this->slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '');
        $newFileName = substr((string)$safeFileName, 0, 23) . '-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $newFileName);
        } catch (FileException) {
            throw new CannotWriteFileException();
        }

        return $newFileName;
    }

    public function delete(string $filename): void
    {
        unlink($this->getAbsolutePath($filename));
    }

    private function getAbsolutePath(string $filename): string
    {
        return $this->uploadDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}