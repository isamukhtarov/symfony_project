<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Twig\Environment;

class ExtractVotes
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function handle(string $content, Closure $next): string
    {
        preg_match_all('/{{vote-(?P<voteIds>[0-9]+)}}/', $content, $match);

        foreach ($match['voteIds'] as $voteId) {
            $view = $this->twig->render('@RiaWeb/post/partials/vote.html.twig', compact('voteId'));

            $content = str_replace("{{vote-{$voteId}}}", $view, $content);
        }

        return $next($content);
    }

}