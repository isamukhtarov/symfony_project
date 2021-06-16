<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Symfony\Component\Validator\Constraints as Assert;

class CropPhotoCommand
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Type('double')]
    public float $x;

    #[Assert\NotBlank]
    #[Assert\Type('double')]
    public float $y;

    #[Assert\NotBlank]
    #[Assert\Type('double')]
    public float $width;

    #[Assert\NotBlank]
    #[Assert\Type('double')]
    public float $height;

    public function __construct(Photo $photo, array $properties)
    {
        $this->id = $photo->getId();

        foreach ($properties as $property => $value) {
            $this->$property = (float) $value;
        }
    }

}