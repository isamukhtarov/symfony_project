<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service\Archive;

use ZipArchive;
use RuntimeException;
use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostPhotosArchiveService
{
    private Post $post;
    private ZipArchive $zip;

    #[Pure] public function __construct(
        private ImagePackage $imagePackage,
        private LoggerInterface $logger,
    )
    {
        $this->zip = new ZipArchive();
    }

    public function archive(Collection $photos): ArchiveDto
    {
        $fileName = $this->post->getSlug() . '.zip';
        $path = '/tmp/' . $fileName;

        if ($this->zip->open($path, ZIPARCHIVE::CREATE) !== true)
            throw new RuntimeException('Could not create archive: ' . $path);

        $this->pack($photos);
        $this->zip->close();

        return new ArchiveDto($fileName, $path);
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    private function pack(Collection $photos): void
    {
        foreach ($photos as $photo) {
            try {
                $this->zip->addFile($this->imagePackage->getAbsolutePath($photo->getFilename()), $photo->getFilename());
            } catch (FileException $e) {
                $this->logger->error('Error occurred while archiving photos', compact('e'));
                continue;
            }
        }
    }
}