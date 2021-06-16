<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use League\Tactician\CommandBus;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Form\Grid\RegionGrid;
use Ria\Bundle\PostBundle\Form\Type\Region\RegionType;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ria\Bundle\PostBundle\Command\Region\{CreateRegionCommand, MoveDownRegionCommand, MoveUpRegionCommand, UpdateRegionCommand, DeleteRegionCommand};

#[Route('posts/regions', name: 'regions.')]
class RegionController extends AbstractController
{
    public function __construct(
        private CommandBus $bus,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(RegionGrid $grid): Response
    {
        return $this->render('@RiaPost/regions/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateRegionCommand($this->getParameter('app.supported_locales'));

        $form = $this->createForm(RegionType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('regions.index');
        }

        return $this->render('@RiaPost/regions/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Region $region, Request $request): Response
    {
        $command = new UpdateRegionCommand($region, $this->getParameter('app.supported_locales'));

        $form = $this->createForm(RegionType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('regions.index');
        }

        return $this->render('@RiaPost/regions/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $this->bus->handle(new DeleteRegionCommand((int) $id));
        return $this->redirectToRoute('regions.index');
    }

    #[Route('/{id}/move-up', name: 'move-up', methods: ['GET'])]
    public function moveUp($id): Response
    {
        $this->bus->handle(new MoveUpRegionCommand((int) $id));
        return $this->redirectToRoute('regions.index');
    }

    #[Route('/{id}/move-down', name: 'move-down', methods: ['GET'])]
    public function moveDown($id): Response
    {
        $this->bus->handle(new MoveDownRegionCommand((int) $id));
        return $this->redirectToRoute('regions.index');
    }
}