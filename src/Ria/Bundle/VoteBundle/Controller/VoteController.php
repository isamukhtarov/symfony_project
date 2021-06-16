<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\VoteBundle\Command\CreateVoteCommand;
use Ria\Bundle\VoteBundle\Command\DeleteVoteCommand;
use Ria\Bundle\VoteBundle\Command\UpdateVoteCommand;
use Ria\Bundle\VoteBundle\Dto\VoteDto;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Ria\Bundle\VoteBundle\Enum\VotesPermissions;
use Ria\Bundle\VoteBundle\Form\Grid\VoteGrid;
use Ria\Bundle\VoteBundle\Form\Type\VoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('votes', name: 'votes.')]
class VoteController extends AbstractController
{
    public function __construct(
        private CommandBus $bus
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(VoteGrid $grid): Response
    {
//        $this->denyAccessUnlessGranted(VotesPermissions::MANAGE);

        return $this->render('@RiaVote/vote/index.html.twig', [
            'grid' => $grid->createView()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $requestOptions = isset($request->get('vote')['options']) ? $request->get('vote')['options'] : [];
        $command = new CreateVoteCommand(new VoteDto($request->get('lang'), $requestOptions));

        $form = $this->createForm(VoteType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('votes.index', ['filter_v.language' => $request->get('lang')]);
        }

        return $this->render('@RiaVote/vote/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Vote $vote, Request $request): Response
    {
        $requestOptions = !empty($request->request->get('vote')['options']) ? $request->request->get('vote')['options'] : [];
        $command = new UpdateVoteCommand($vote, $requestOptions);

        $form = $this->createForm(VoteType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('votes.index', ['filter_v.language' => $vote->getLanguage()]);
        }
        $savedOptions = $vote->getOptions()->toArray();

        return $this->render('@RiaVote/vote/form.html.twig', [
            'form' => $form->createView(),
            'options' => $savedOptions
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $this->bus->handle(new DeleteVoteCommand($id));
        return $this->redirectToRoute('votes.index');
    }
}