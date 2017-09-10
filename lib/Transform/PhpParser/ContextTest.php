<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform\PhpParser;

use PHPUnit\Framework\TestCase;
use PhpParser\Node;

class ContextTest extends Testcase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Attempting to pop an empty stack
     */
    public function testPopOnEmptyStack()
    {
        $context = new Context;
        $node = $this->createMock(Node::class);
        $context->pop($node);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Attempting to pop an empty stack
     */
    public function testPopOnEmptiedStack()
    {
        $context = new Context;
        $node = $this->createMock(Node::class);
        $context->push($node);
        $context->pop($node);
        $context->pop($node);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Stack is out of sync
     */
    public function testPopMismatchedStack()
    {
        $context = new Context;
        $node1 = $this->createMock(Node::class);
        $node2 = $this->createMock(Node::class);
        $context->push($node1);
        $context->pop($node2);
    }

    public function testPushPop()
    {
        $context = new Context;
        $node1 = $this->createMock(Node::class);
        $node2 = $this->createMock(Node::class);
        $context->push($node1);
        $context->push($node2);
        $this->assertSame($node2, $context->pop($node2));
        $this->assertSame($node1, $context->pop($node1));
    }

    public function testPeek()
    {
        $context = new Context;
        $node = $this->createMock(Node::class);
        $context->push($node);
        $this->assertSame($node, $context->peek());
    }

    public function testPeek2()
    {
        $context = new Context;
        $node1 = $this->createMock(Node::class);
        $context->push($node1);
        $node2 = $this->createMock(Node::class);
        $context->push($node2);
        $this->assertSame($node2, $context->peek());
        $this->assertSame($node1, $context->peek(1));
    }

    public function testResolveNamespaceRawClassName()
    {
        $context = new Context;
        $namespace = new Node\Stmt\Namespace_(new Node\Name("Foo\\Bar"));
        $context->push($namespace);
        $name = Node\Name::concat("Baz", null);
        $result = $context->resolveClass($name);
        $this->assertEquals("Foo\\Bar\\Baz", $result->toString());
    }

    public function testResolveNamespaceSpecialClassName()
    {
        $context = new Context;
        $namespace = new Node\Stmt\Namespace_(new Node\Name("Foo\\Bar"));
        $context->push($namespace);
        $name = Node\Name::concat("self", null);
        $result = $context->resolveClass($name);
        $this->assertEquals("self", $result->toString());
    }

    public function testResolveNamespaceAbsoluteClassName()
    {
        $context = new Context;
        $namespace = new Node\Stmt\Namespace_(new Node\Name("Foo\\Bar"));
        $context->push($namespace);
        $name = Node\Name\FullyQualified::concat("Baz", null);
        $result = $context->resolveClass($name);
        $this->assertEquals("Baz", $result->toString());
    }

    public function testResolveNamespaceUsedClassName()
    {
        $context = new Context;
        $namespace = new Node\Stmt\Namespace_(new Node\Name("Foo\\Bar"));
        $context->push($namespace);
        $use = new Node\Stmt\Use_(
          [new Node\Stmt\UseUse(Node\Name::concat("Biz\\Buz", null))]
        );
        $context->push($use);
        $name = Node\Name::concat("Buz\\Baz", null);
        $result = $context->resolveClass($name);
        $this->assertEquals("Biz\\Buz\\Baz", $result->toString());
    }

    public function testResolveNamespaceUnusedClassName()
    {
        $context = new Context;
        $namespace = new Node\Stmt\Namespace_(new Node\Name("Foo\\Bar"));
        $context->push($namespace);
        $use = new Node\Stmt\Use_(
          [new Node\Stmt\UseUse(Node\Name::concat("Biz\\Buz", null))]
        );
        $context->push($use);
        $name = Node\Name::concat("Blah\\Baz", null);
        $result = $context->resolveClass($name);
        $this->assertEquals("Foo\\Bar\\Blah\\Baz", $result->toString());
    }
}
