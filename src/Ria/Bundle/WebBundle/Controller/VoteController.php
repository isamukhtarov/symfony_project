<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\VoteBundle\Command\AddVoteCommand;
use Ria\Bundle\VoteBundle\Query\Resource\VoteResource;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoteController extends AbstractController
{
    private ?Request $request;

    public function __construct(
        private VoteRepository $voteRepository,
        private VoteResource $voteResource,
        private SerializerInterface $serializer,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    #[Route("/votes-archive", name: "vote.archive", methods: ['GET'], priority: 10)]
    public function index(Request $request): Response
    {
        $votes = $this->voteRepository->getForVotesPage($this->request->getLocale(), 6, $request->get('id'));

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html'         => $this->renderView('@RiaWeb/index/votes.html.twig', compact('votes')),
                'limitReached' => count($votes) < 6
            ]);
        }

        $urlTranslations = $this->getUrlTranslations();

        return $this->render('@RiaWeb/index/votes.html.twig', compact('votes', 'urlTranslations'));
    }

    #[Route("/vote/show", name: "vote.show", methods: ['POST'])]
    public function show(Request $request, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $vote = $this->voteRepository->getById((int) $request->query->get('id'));

        return $this->json([
            'vote' => $vote ? $this->voteResource->transform($vote) : null,
            'voteAddUrl' => $urlGenerator->generate('vote.add'),
            'votesArchiveUrl' => $urlGenerator->generate('vote.archive'),
        ]);
    }

    #[Route("/vote/add", name: "vote.add", methods: ['POST'])]
    public function addVote(Request $request, CommandBus $bus, ValidatorInterface $validator): JsonResponse
    {
        $command = new AddVoteCommand();
        $command->voteId = (int) $request->request->get('voteId');
        $command->optionId = (int) $request->request->get('optionId');

        if ($request->request->has('recaptcha')) {
            $command->recaptcha = $request->request->get('recaptcha');
        }

        $violations = $validator->validate($command);

        if ($violations->count() > 0) {
            $errors = $this->serializer->serialize($violations, 'json');
            return $this->json(['success' => false, 'errors' => $errors], 422);
        }

        $bus->handle($command);
        $vote = $this->voteRepository->getById($command->voteId);

        $response = $this->json([
            'success' => true,
            'data'    => [
                'vote'     => $this->voteResource->transform($vote),
                'selected' => $command->optionId,
            ],
        ]);

        $cookie = $this->processCookie($command->voteId, $command->optionId);

        if ($cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    private function processCookie(int $votedId, int $optionId): ?Cookie
    {
        $voted = $this->getVoted();

        // check if already exist on cookie
        if (collect($voted)->where('vote', $votedId)->first()) {
            return null;
        }

        $voted[] = [
            'vote'   => $votedId,
            'option' => $optionId
        ];

        return Cookie::create(
            name: 'voted',
            value: json_encode($voted, JSON_FORCE_OBJECT),
            expire: time() + 30 * 86400, // 1 month
            httpOnly: false
        );
    }

    private function getVoted(): array
    {
        $votedCookie = $this->request->cookies->get('voted');

        if (empty($votedCookie)) {
            return [];
        }

        try {
            return $this->serializer->decode((string) $votedCookie, 'json', [JsonDecode::ASSOCIATIVE => true]);
        } catch (NotEncodableValueException $exception) {
            return [];
        }
    }

    protected function getUrlTranslations(): array
    {
        $urls = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urls[$language] = $this->generateUrl(
                'vote.archive',
                ['_locale' => $language],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $urls;
    }

}