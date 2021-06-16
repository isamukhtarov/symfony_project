<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\PhotoBundle\Command\CreatePhotoCommand;
use Ria\Bundle\PhotoBundle\Command\CropPhotoCommand;
use Ria\Bundle\PhotoBundle\Command\DeletePhotoCommand;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Ria\Bundle\PhotoBundle\Command\UpdatePhotoCommand;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Form\Type\PhotoType;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('photos', name: 'photos.')]
class PhotoController extends AbstractController
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private CommandBus $bus,
    )
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $lastTimestamp = $request->get('created_at');
        $searchQuery = $request->query->get('q');
        $photos = $this->photoRepository->list($searchQuery, $lastTimestamp);

        return $this->json([
            'content' => $this->renderView('@RiaPhoto/index.html.twig', compact('photos'))
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $command           = new CreatePhotoCommand();
        $command->image    = $request->files->get('image');
        $command->withLogo = (bool)$request->request->get('withLogo');

        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            $json = $this->serializer->serialize($violations, 'json');
            return $this->json($json, 422);
        }

        $this->bus->handle($command);

        return $this->json([
            'success' => true,
            'photo'   => $this->renderView('@RiaPhoto/photo_block.html.twig', ['photo' => $this->photoRepository->latestOne()])
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Photo $photo, Request $request): JsonResponse
    {
        $command = new UpdatePhotoCommand($photo, $this->getParameter('app.supported_locales'));

        $form = $this->createForm(PhotoType::class, $command);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $this->bus->handle($command);

            return $this->json(['success' => true]);
        }

        return $this->json([
            'success' => true,
            'form'    => $this->renderView('@RiaPhoto/form/update.html.twig', [
                'photo' => $photo,
                'form'  => $form->createView(),
            ])
        ]);
    }

    #[Route('/{id}/crop', name: 'crop', methods: ['POST'])]
    public function crop(Photo $photo, Request $request): JsonResponse
    {
        $data       = $request->request->all();
        $command    = new CropPhotoCommand($photo, $data);
        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            return $this->json([
                'success' => false,
                'errors'  => $this->container->get('serializer')->serialize($violations, 'json'),
            ]);
        }

        $this->bus->handle($command);

        return $this->json(['success' => true]);
    }

    #[Route('/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {
        $this->bus->handle(new DeletePhotoCommand((int)$request->query->get('id')));
        return $this->json(['success' => true]);
    }

    #[Route('/form-blocks', name: 'form_blocks', methods: ['POST'])]
    public function formBlocks(Request $request): JsonResponse
    {
        $photos = $this->photoRepository->findById($request->request->get('ids'));
        $main   = $request->request->has('main')
            ? $this->photoRepository->find($request->request->get('main'))
            : null;

        $command = new PhotoCommand($photos, $main);

        return $this->json([
            'content' => $this->renderView('@RiaPhoto/form/form_cards.html.twig', [
                'value'     => $command,
                'full_name' => $request->request->get('formName')
            ])
        ]);
    }

}