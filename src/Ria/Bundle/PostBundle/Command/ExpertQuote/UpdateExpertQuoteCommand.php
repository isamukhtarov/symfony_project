<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\ExpertQuote;

use JetBrains\PhpStorm\Pure;

class UpdateExpertQuoteCommand extends QuoteCommand
{
    public int $id;

    #[Pure]public function __construct(int $postId, int $expertId, string $text, int $id)
    {
        parent::__construct($postId, $expertId, $text);
        $this->id = $id;
    }
}