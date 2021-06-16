<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;
use Twig\Environment;

class StoryWidget extends FrontendWidget
{

    private StoryRepository $storyRepository;

    public function __construct(Environment $twig, StoryRepository $storyRepository) {
        $this->storyRepository = $storyRepository;

        parent::__construct($twig);
    }

    public function run(): string
    {
        $stories = $this->storyRepository->match($this->filters);

        return $this->render($this->template, compact('stories'));
    }

}