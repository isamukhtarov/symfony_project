<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Command\Story\CreateStoryCommand;
use Ria\Bundle\PostBundle\Command\Story\DeleteStoryCommand;
use Ria\Bundle\PostBundle\Command\Story\UpdateStoryCommand;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Ria\Bundle\PostBundle\Form\Grid\StoryGrid;
use Ria\Bundle\PostBundle\Enum\StoryPermissions;
use Ria\Bundle\PostBundle\Form\Type\Story\StoryType;
use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('posts/stories', name: 'stories.')]
class StoryController extends AbstractController
{
    // todo manageStories permission

    public function __construct(
        private StoryRepository $storyRepository,
        private CommandBus $bus,
    ) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(StoryGrid $grid): Response
    {
//        $this->denyAccessUnlessGranted(StoryPermissions::MANAGE);

        return $this->render('@RiaPost/stories/index.html.twig', [
            'grid' => $grid->createView()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateStoryCommand($this->getParameter('app.supported_locales'));

        $form = $this->createForm(StoryType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('stories.index');
        }

        return $this->render('@RiaPost/stories/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Story $story, Request $request): Response
    {
        $command = new UpdateStoryCommand($story, $this->getParameter('app.supported_locales'));

        $form = $this->createForm(StoryType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('stories.index');
        }

        return $this->render('@RiaPost/stories/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $this->bus->handle(new DeleteStoryCommand((int) $id));
        return $this->redirectToRoute('stories.index');
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function actionList(string $language, $q = null): JsonResponse
    {
        $stories = $this->storyRepository->searchByTitle($language, $q);

        return $this->json(['results' => $stories]);
    }
}