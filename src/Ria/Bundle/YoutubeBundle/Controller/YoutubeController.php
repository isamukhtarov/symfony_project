<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\YoutubeBundle\Command\YoutubeCreateCommand;
use Ria\Bundle\YoutubeBundle\Command\YoutubeDeleteCommand;
use Ria\Bundle\YoutubeBundle\Command\YoutubeUpdateCommand;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;
use Ria\Bundle\YoutubeBundle\Form\Grid\YoutubeGrid;
use Ria\Bundle\YoutubeBundle\Form\Type\YoutubeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('youtube', name: 'youtube.')]
class YoutubeController extends AbstractController
{
    public function __construct(
        private CommandBus $bus
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(YoutubeGrid $grid): Response
    {
        return $this->render('@RiaYoutube/index.html.twig', [
            'grid' => $grid->createView()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new YoutubeCreateCommand();

        $form = $this->createForm(YoutubeType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->bus->handle($command);
            return $this->redirectToRoute('youtube.index');
        }

        return $this->render('@RiaYoutube/form.html.twig', [
            'form' => $form->createView(),
            'youtubeId' => ''
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(YouTube $youTube, Request $request): Response
    {
        $command = new YoutubeUpdateCommand($youTube);

        $form = $this->createForm(YoutubeType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->bus->handle($command);
            return $this->redirectToRoute('youtube.index');
        }

        return $this->render('@RiaYoutube/form.html.twig', [
            'form' => $form->createView(),
            'youtubeId' => $youTube->getYoutubeId()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $this->bus->handle(new YoutubeDeleteCommand($id));
        return $this->redirectToRoute('youtube.index');
    }
}