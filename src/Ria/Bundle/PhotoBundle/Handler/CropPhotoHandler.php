<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Handler;

use Ria\Bundle\PhotoBundle\Command\CropPhotoCommand;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Ria\Bundle\PhotoBundle\Service\ImageManipulatorInterface;

class CropPhotoHandler
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private ImageManipulatorInterface $imageManipulator,
    ) {}

    public function handle(CropPhotoCommand $command)
    {
        $photo = $this->photoRepository->find($command->id);

        $this->imageManipulator->crop(
            width: $command->width,
            height: $command->height,
            x: $command->x,
            y: $command->y,
            filename: $photo->getFilename()
        );

        $this->imageManipulator->dropUniCropperCache($photo->getFilename());
    }

}