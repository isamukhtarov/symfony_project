<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class ExtractVote
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractVote
{

    public function handle(string $content, Closure $next): string
    {
        foreach ($this->getVoteIds($content) as $voteId) {
            $content = str_replace(
                "{{vote-{$voteId}}}",
                VoteWidget::widget(['voteId' => (int)$voteId, 'type' => VoteWidget::TYPE_POST]),
                $content);
        }

        return $next($content);
    }

    protected function getVoteIds(string $content): array
    {
        preg_match_all('/{{vote-(?P<voteId>[0-9]+)}}/', $content, $match);
        return $match['voteId'];
    }

}