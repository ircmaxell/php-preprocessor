<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PhpParser\Parser;
use PhpParser\ParserFactory as CoreParserFactory;

class StandardParserFactory implements ParserFactory
{
    public function getParser(): Parser
    {
        return (new CoreParserFactory)->create(
            CoreParserFactory::ONLY_PHP7
        );
    }
}
