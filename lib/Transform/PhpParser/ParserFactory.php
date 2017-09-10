<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PhpParser\Parser;

interface ParserFactory
{
    public function getParser(): Parser;
}
