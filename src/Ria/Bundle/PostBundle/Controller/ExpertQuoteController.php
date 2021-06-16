<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use JetBrains\PhpStorm\ArrayShape;
use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Command\ExpertQuote\CreateExpertQuoteCommand;
use Ria\Bundle\PostBundle\Command\ExpertQuote\UpdateExpertQuoteCommand;
use Ria\Bundle\PostBundle\Entity\ExpertQuote\ExpertQuote;
use Ria\Bundle\PostBundle\Repository\ExpertQuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('post/quote', name: 'quote.')]
class ExpertQuoteController extends AbstractController
{
    public function __construct(
        private ExpertQuoteRepository $expertQuoteRepository,
        private CommandBus $bus
    ){}

    #[Route('/get/{id}/language/{language}', name: 'quote.', methods: ['GET'])]
    public function index(int $id, string $language): JsonResponse
    {
        $quote = $this->expertQuoteRepository->find($id);
        return $this->json([
            'success' => true,
            'quote' => $this->getNormalizedQuote($quote, $language)
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $expertId = (int)$request->request->get('expertId');
        $text = (string)$request->request->get('text');
        $postId = (int)$request->request->get('postId');

        $command = new CreateExpertQuoteCommand($postId, $expertId, $text);

        if (empty($command->expertId) || empty($command->text)) {
            return $this->json([
                'success' => false,
                'message' => 'Expert and text fields required'
            ], 422);
        }
        $this->bus->handle($command);

        $quote = $this->expertQuoteRepository->latestOne();

        return $this->json([
            'success' => true,
            'quote' => $this->getNormalizedQuote($quote, $request->request->get('language'))
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $expertId = (int)$request->request->get('expertId');
        $text = (string)$request->request->get('text');
        $postId = (int)$request->request->get('postId');

        $command = new UpdateExpertQuoteCommand($postId, $expertId, $text, $id);

        if (empty($command->expertId) || empty($command->text)) {
            return $this->json([
                'success' => false,
                'message' => 'Expert and text fields required'
            ], 422);
        }
        $this->bus->handle($command);

        return $this->json([
            'success' => true
        ]);
    }

    #[ArrayShape([
        'id' => 'int',
        'expert' => 'array',
        'text' => 'string'
    ])]
    protected function getNormalizedQuote(ExpertQuote $quote, string $language): ?array
    {
        $expert            = $quote->getExpert();
        $expertTranslation = $expert->getTranslation($language);

        return [
            'id'     => $quote->getId(),
            'expert' => [
                'id'       => $expert->getId(),
                'name'     => $expertTranslation->getFirstName() . ' ' . $expertTranslation->getLastName(),
                'position' => $expertTranslation->getPosition()
            ],
            'text'   => $quote->getText()
        ];
    }

}