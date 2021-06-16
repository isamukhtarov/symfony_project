<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\ExpertQuote;

class QuoteCommand
{
    public int $postId;
    public int $expertId;
    public string $text;

    public function __construct(int $postId, int $expertId, string $text)
    {
        $this->postId = $postId;
        $this->expertId = $expertId;
        $this->text = $text;
    }
}