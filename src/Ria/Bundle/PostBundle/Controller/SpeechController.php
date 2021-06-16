<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Command\Speech\CreateSpeechCommand;
use Ria\Bundle\PostBundle\Command\Speech\DeleteSpeechCommand;
use Ria\Bundle\PostBundle\Command\Speech\UpdateSpeechCommand;
use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('posts/speech', name: 'speech.')]
class SpeechController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommandBus $bus,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    ){}

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $command = new CreateSpeechCommand();
        if (empty($request->files->get('file'))) {
            return $this->fileRequiredResponse();
        }
        $command->postId = (int)$request->get('postId');
        $command->file = $request->files->get('file');

        $violations = $this->validator->validate($command);

        if (count($violations) > 0) {
            return $this->json($this->serializer->serialize($violations, 'json'), 422);
        }

        $this->bus->handle($command);

        $speech = $this->getSpeechByPost((int)$command->postId);

        return $this->json([
            'success' => true,
            'speech' => ['id' => $speech->getId()],
            'player' => $this->renderView('@RiaPost/posts/partials/audio.html.twig', ['speech' => $speech]),
            'updateUrl' => $this->generateUrl('speech.update')
        ]);
    }

    #[Route('/update', name: 'update', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        $command = new UpdateSpeechCommand();
        if (empty($request->files->get('file'))) {
            return $this->fileRequiredResponse();
        }

        $command->id = (int)$request->get('id');
        $command->file = $request->files->get('file');

        $violations = $this->validator->validate($command);

        if (count($violations) > 0) {
            return $this->json($this->serializer->serialize($violations, 'json'), 422);
        }

        $this->bus->handle($command);

        $speech = $this->getSpeechByPost((int)$request->get('postId'));

        return $this->json([
            'success' => true,
            'speech' => ['id' => $speech->getId()],
            'player' => $this->renderView('@RiaPost/posts/partials/audio.html.twig', ['speech' => $speech]),
            'updateUrl' => $this->generateUrl('speech.update')
        ]);
    }

    #[Route('/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {
        $this->bus->handle(new DeleteSpeechCommand((int) $request->request->get('id')));
        return $this->json([
            'success' => 'Speech deleted successfully',
            'createUrl' => $this->generateUrl('speech.create')
        ]);
    }

    public function getSpeechByPost(int $postId): object
    {
        return $this->entityManager->getRepository(Speech::class)->findOneBy(['post' => $postId]);
    }

    public function fileRequiredResponse(): JsonResponse
    {
        return $this->json([
            'success' => false,
            'message' => 'File required'
        ]);
    }
}