<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\PostBundle\Repository\TagRepository;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tags', name: 'tags.')]
class TagController extends AbstractController
{
    public function __construct(
        private TagRepository $tagRepository,
    ){}

    #[Route('/list', name: 'list', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function list(Request $request): Response
    {
        return $this->json($this->tagRepository->list(
            $request->get('term'), $request->get('language', $request->getLocale())
        ));
    }
}