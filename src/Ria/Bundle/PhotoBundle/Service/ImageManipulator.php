<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Service;

use GuzzleHttp\Client;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\{FileException, CannotWriteFileException};

class ImageManipulator implements ImageManipulatorInterface
{
    public function __construct(
        private string $uploadDirectory,
        private SluggerInterface $slugger,
        private ImagePackage $package,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename, '');
        $newFilename = substr((string) $safeFilename, 0, 23) . '-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $newFilename);
        } catch (FileException) {
            throw new CannotWriteFileException();
        }

        return $newFilename;
    }

   public function crop(float|int $width, float|int $height, float|int $x, float|int $y, string $filename): void
   {
       $cmd = sprintf(
           "gm mogrify -crop %sx%s+%s+%s %s",
           $width, $height, $x, $y, $this->getAbsolutePath($filename)
       );

       shell_exec($cmd);
   }

    public function addWatermark(string $filename, string $watermarkPath): void
    {
        $cmd = "gm composite -dissolve 80% -gravity South -geometry +0+50 {$watermarkPath} {$this->getAbsolutePath($filename)} {$this->getAbsolutePath($filename)}";

        shell_exec($cmd);
    }

    public function delete(string $filename): void
    {
        unlink($this->getAbsolutePath($filename));
    }

    public function dropUniCropperCache(string $filename): void
    {
        // todo docker curl error fix
//        $client = new Client();
//        $client->request('purge', $this->package->getUrl($filename, ['thumb' => 290]));
    }

    private function getAbsolutePath(string $filename): string
    {
        return $this->uploadDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}