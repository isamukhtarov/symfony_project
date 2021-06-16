<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class VoteWidget extends FrontendWidget
{
    private ?Request $request;

    public function __construct(
        protected Environment $twig,
        private VoteRepository $voteRepository,
        private PostRepository $postRepository,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function run(): string
    {
        $vote = $this->voteRepository->getOnePrizeType($this->request->getLocale());

        return $this->render($this->template, compact('vote'));
    }
}