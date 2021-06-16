<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Command\Widget\CreateWidgetCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('posts/widget', name: 'widget.')]
class WidgetController extends AbstractController
{
    public function __construct(
        private CommandBus $bus
    ){}

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $content = $request->request->get('content');

        if (empty($content)) {
            return $this->getRequiredResponse();
        }

        $command = new CreateWidgetCommand($content);
        $this->bus->handle($command);

        return $this->json([
            'success' => true,
            'token' => sprintf('{{widget-content-%d}}', $command->id)
        ]);

    }

    private function getRequiredResponse(): JsonResponse
    {
        return $this->json([
            'success' => false,
            'message' => 'Content is required'
        ]);
    }
}