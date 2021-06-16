<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Entity\City\City;
use Ria\Bundle\PostBundle\Form\Grid\CityGrid;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\PostBundle\Form\Type\City\CityType;
use Symfony\Component\HttpFoundation\{
    RedirectResponse,
    Request,
    Response};
use Ria\Bundle\PostBundle\Query\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ria\Bundle\PostBundle\Command\City\{CreateCityCommand, UpdateCityCommand, DeleteCityCommand};

#[Route('posts/cities', name: 'cities.')]
class CityController extends AbstractController
{
    public function __construct(
        private CityRepository $cityRepository,
        private CommandBus $bus,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CityGrid $grid): Response
    {
        return $this->render('@RiaPost/cities/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateCityCommand($this->getParameter('app.supported_locales'), $request->getLocale());
        $form = $this->createForm(CityType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('cities.index');
        }

        return $this->render('@RiaPost/cities/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(City $city, Request $request): Response
    {
        $command = new UpdateCityCommand($city, $this->getParameter('app.supported_locales'), $request->getLocale());

        $form = $this->createForm(CityType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isSubmitted()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('cities.index');
        }

        return $this->render('@RiaPost/cities/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id): RedirectResponse
    {
        $this->bus->handle(new DeleteCityCommand($id));
        return $this->redirectToRoute('cities.index');
    }
}