<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\PersonBundle\Command\Person\CreatePersonCommand;
use Ria\Bundle\PersonBundle\Command\Person\DeletePersonCommand;
use Ria\Bundle\PersonBundle\Command\Person\UpdatePersonCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Entity\Person\Type;
use Ria\Bundle\PersonBundle\Form\Grid\PersonGrid;
use Ria\Bundle\PersonBundle\Form\Type\Person\PersonType;
use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('persons', name: 'persons.')]
class PersonController extends AbstractController
{
    public function __construct(
        private PersonRepository $personsRepository,
        private CommandBus $bus,
        private PhotoRepository $photoRepository,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PersonGrid $grid)
    {
        return $this->render('@RiaPerson/persons/index.html.twig', [
             'grid' => $grid->createView()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request) : Response
    {
        $command = new CreatePersonCommand(Type::PERSON, $this->getParameter('app.supported_locales'));

        $form = $this->createForm(PersonType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('persons.index');
        }

        return $this->render('@RiaPerson/persons/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Person $person, Request $request) : Response
    {
        $command = new UpdatePersonCommand($person, $this->getParameter('app.supported_locales'));

        $form = $this->createForm(PersonType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('persons.index');
        }

        return $this->render('@RiaPerson/persons/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/list', name: 'list', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function list(Request $request): Response
    {
        return $this->json($this->personsRepository->list(
            $request->get('term'), $request->get('language', $request->getLocale()), $request->get('type')
        ));
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(int $id) : Response
    {
        $this->bus->handle(new DeletePersonCommand($id));
        return $this->redirectToRoute('persons.index');
    }
}