<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePhotoCommand
{
    public bool $withLogo = false;

    #[Assert\Image(maxSize: 10000000)]
    public UploadedFile $image;

}