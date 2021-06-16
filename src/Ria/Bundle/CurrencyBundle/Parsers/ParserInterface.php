<?php
declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Parsers;

use Generator;

interface ParserInterface
{
    public function parse(): Generator|array;
}