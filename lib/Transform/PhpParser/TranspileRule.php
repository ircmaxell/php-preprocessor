<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PhpParser\Node;

interface TranspileRule
{
    public function getNodeTypes(): array;

    public function transpile(Node $node, Context $ctx);
}
