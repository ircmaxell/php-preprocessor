<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PHPUnit\Framework\TestCase;
use PhpParser\Parser;

class StandardParserFactoryTest extends Testcase
{
    public function testGetParser()
    {
        $factory = new StandardParserFactory;
        $this->assertInstanceOf(Parser::class, $factory->getParser());
    }
}
