<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser\TranspileRule;

use PhpPreprocessor\Transform\PhpParser\Context;
use PhpPreprocessor\Transform\PhpParser\TranspileRule;
use PhpParser\Node;

class CallableRule implements TranspileRule
{
    protected $nodeTypes = [];
    protected $cb = null;

    public function __construct(callable $cb, array $nodeTypes)
    {
        $this->nodeTypes = $nodeTypes;
        $this->cb = $cb;
    }

    public function getNodeTypes(): array
    {
        return $this->nodeTypes;
    }

    public function transpile(Node $node, Context $ctx)
    {
        return ($this->cb)($node, $ctx);
    }
}
