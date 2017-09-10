<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name\FullyQualified;

use RuntimeException;

class Context
{
    const DEFAULT_USE_MAP = [
        Stmt\Use_::TYPE_UNKNOWN => [],
        Stmt\Use_::TYPE_NORMAL => [],
        Stmt\Use_::TYPE_CONSTANT => [],
        Stmt\Use_::TYPE_FUNCTION => [],
    ];

    protected $useMap = self::DEFAULT_USE_MAP;
    protected $namespace = '';

    protected $stack = [];

    public function push(Node $node)
    {
        $this->stack[] = $node;
        if ($node instanceof Stmt\Namespace_) {
            $this->resetState($node->name);
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, null);
            }
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, $node->prefix);
            }
        }
    }

    public function pop(Node $node)
    {
        if (empty($this->stack)) {
            throw new RuntimeException("Attempting to pop an empty stack");
        }
        if ($node !== array_pop($this->stack)) {
            throw new RuntimeException("Stack is out of sync, unexpected node");
        }
        if (empty($this->stack)) {
            $this->resetState();
        }
        return $node;
    }

    public function peek(int $offset = 0)
    {
        $result = array_slice($this->stack, -1 * $offset - 1, 1);
        if (!empty($result)) {
            return end($result);
        }
    }

    public function findByClass(string $class, int $offset = 0)
    {
        for ($i = count($this->stack) - 1; $i >= 0; $i--) {
            if ($this->stack[$i] instanceof $class) {
                if ($offset-- > 0) {
                    continue;
                }
                return $class;
            }
        }
    }

    public function resolveClass(Name $name): Name
    {
        if (in_array(strtolower($name->toString()), ["self", "parent", "static"])) {
            return $name;
        }
        return $this->resolveType($name, Stmt\Use_::TYPE_NORMAL);
    }

    public function resolveConstant(Name $name): Name
    {
        return $this->resolveTypeName($name, Stmt\Use_::TYPE_CONSTANT);
    }

    public function resolveFunction(Name $name): Name
    {
        return $this->resolveTypeName($name, Stmt\Use_::TYPE_FUNCTION);
    }

    protected function resolveType(Name $name, int $type): Name
    {
        if ($name->isFullyQualified()) {
            return $name;
        }
        $aliasName = $type === Stmt\Use_::TYPE_CONSTANT ? $name->getFirst() :strtolower($name->getFirst());

        $namespace = $this->namespace;
        if (!$name->isRelative() && isset($this->aliases[$type][$aliasName])) {
            $namespace = $this->aliases[$type][$aliasName];
            $name = $name->slice(1);
        }

        return FullyQualified::concat($namespace, $name, $name->getAttributes());
    }



    protected function resetState(Name $name = null)
    {
        $this->useMap = self::DEFAULT_USE_MAP;
        $this->namespace = $name;
    }

    protected function addAlias(Stmt\UseUse $use, $type, Name $prefix = null)
    {
        $name = $prefix ? Name::concat($prefix, $use->name) : $use->name;
        $type |= $use->type;

        if ($type === Stmt\Use_::TYPE_CONSTANT) {
            $aliasName = $use->alias;
        } else {
            $aliasName = strtolower($use->alias);
        }

        if (isset($this->aliases[$type][$aliasName])) {
            throw new RuntimeException("Cannot use $name as name is already in use");
        }

        $this->aliases[$type][$aliasName] = $name;
    }
}
