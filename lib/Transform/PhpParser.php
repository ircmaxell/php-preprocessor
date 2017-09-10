<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform;

use PhpPreprocessor\Transform;
use PhpPreprocessor\Transform\PhpParser\NodeTraverser;
use PhpPreprocessor\Transform\PhpParser\ParserFactory;
use PhpPreprocessor\Transform\PhpParser\StandardParserFactory;
use PhpPreprocessor\Transform\PhpParser\TranspileRule;
use PhpParser\PrettyPrinterAbstract;
use PhpParser\PrettyPrinter\Standard;

class PhpParser implements Transform
{
    protected $nodeTraverser;
    protected $printer;
    protected $factory;
    protected $parser;

    public function __construct(ParserFactory $factory = null, PrettyPrinterAbstract $printer = null)
    {
        //ignore process variables
        $this->nodeTraverser = new NodeTraverser;
        $this->printer = $printer ?: new Standard(["shortArraySyntax" => true]);
        $this->factory = $factory ?: new StandardParserFactory;
    }

    public function addTranspileRule(TranspileRule $rule): self
    {
        $this->nodeTraverser->addTranspileRule($rule);
        return $this;
    }

    public function transform(string $data): string
    {
        if (!$this->parser) {
            $this->parser = $this->factory->getParser();
        }
        $nodes = $this->parser->parse($data);
        $nodes = $this->nodeTraverser->traverse($nodes);
        return '<?php ' . $this->printer->prettyPrint($nodes);
    }
}
