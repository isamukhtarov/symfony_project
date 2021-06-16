<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Parsers;

use Generator;

interface BrentParserInterface
{
    public function parse(): Generator;
}