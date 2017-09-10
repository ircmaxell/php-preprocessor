<?php
declare(strict_types=1);
use PhpPreprocessor\PreProcessor;
use PhpPreprocessor\Transform;
use PhpPreprocessor\Transform\Json;
use PhpPreprocessor\Transform\Yaml;
use PhpPreprocessor\Transform\PhpParser;
use PhpPreprocessor\Transform\PhpParser\TranspileRule\CallableRule;
use PhpParser\Node;

$parser = new PhpParser;
$parser->addTranspileRule(new CallableRule(
    function($node, $ctx) {
      if ($node->left instanceof Node\Scalar\LNumber && $node->right instanceof Node\Scalar\LNumber) {
          // replace nodes with pre-computed value
          return new Node\Scalar\LNumber($node->left->value + $node->right->value, $node->getAttributes());
      }
    },
    [Node\Expr\BinaryOp\Plus::class]
));

PreProcessor::instance()
    ->addTransform('json', new Json)
    ->addTransform('yaml', new Yaml)
    ->addTransform(PreProcessor::EXT_ALL, $parser)
;