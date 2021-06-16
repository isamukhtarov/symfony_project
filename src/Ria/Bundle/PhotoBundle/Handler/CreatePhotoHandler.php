<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Handler;

use ColorThief\ColorThief;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Entity\Translation;
use Ria\Bundle\PhotoBundle\Command\CreatePhotoCommand;
use Ria\Bundle\PhotoBundle\Service\WatermarkLoader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ria\Bundle\PhotoBundle\Service\ImageManipulatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CreatePhotoHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private ImageManipulatorInterface $imageManipulator,
        private WatermarkLoader $watermarkLoader,
    ) {}

    public function handle(CreatePhotoCommand $command): void
    {
        $photo = (new Photo())
            ->setOriginalFilename(pathinfo($command->image->getClientOriginalName(), PATHINFO_FILENAME))
            ->setResolution($this->getImageResolution($command->image))
            ->setGradientRgb($this->getImageGradientRgb($command->image))
            ->setCreatedAt(new DateTime());

        $imageFilename = $this->imageManipulator->upload($command->image);
        $photo->setFilename($imageFilename);

        foreach ($this->parameterBag->get('app.supported_locales') as $locale) {
            $translation = (new Translation())
                ->setLanguage($locale)
                ->setInformation('')
                ->setAuthor('')
                ->setSource('');

            $photo->addTranslation($translation);
        }

        if ($command->withLogo) {
            $this->watermarkLoader->process($photo);
        }

        $this->entityManager->persist($photo);
        $this->entityManager->flush();
    }

    private function getImageResolution(UploadedFile $image): string
    {
        $sizes = getimagesize($image->getRealPath());

        return implode('x', [$sizes[0], $sizes[1]]);
    }

    private function getImageGradientRgb(UploadedFile $image): string
    {
        $gradientRgb = ColorThief::getColor($image->getRealPath());

        return implode(',', $gradientRgb);
    }

}