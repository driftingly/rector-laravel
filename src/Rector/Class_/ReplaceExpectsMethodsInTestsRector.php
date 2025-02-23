<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ExpectedClassMethodAnalyzer;
use RectorLaravel\NodeFactory\DispatchableTestsMethodsFactory;
use RectorLaravel\ValueObject\ExpectedClassMethodMethodCalls;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\ReplaceExpectsMethodsInTestsRectorTest
 */
class ReplaceExpectsMethodsInTestsRector extends AbstractRector
{
    public function __construct(
        private ExpectedClassMethodAnalyzer $expectedClassMethodAnalyzer,
        private DispatchableTestsMethodsFactory $dispatchableTestsMethodsFactory,
    )
    {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace expectJobs and expectEvents methods in tests', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Testing\TestCase;

class SomethingTest extends TestCase
{
    public function testSomething()
    {
        $this->expectsJobs([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
        $this->expectsEvents(\App\Events\SomeEvent::class);

        $this->get('/');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Testing\TestCase;

class SomethingTest extends TestCase
{
    public function testSomething()
    {
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
        \Illuminate\Support\Facades\Event::fake([\App\Events\SomeEvent::class]);

        $this->get('/');

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeJob::class);
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeOtherJob::class);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SomeEvent::class);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Class_
    {
        if (! $this->isObjectType($node, new ObjectType('\Illuminate\Foundation\Testing\TestCase'))) {
            return null;
        }

        $changes = false;

        foreach ($node->getMethods() as $classMethod) {
            $result = $this->expectedClassMethodAnalyzer->findExpectedJobCallsWithClassMethod($classMethod);

            if (! $result instanceof ExpectedClassMethodMethodCalls) {
                continue;
            }

            if ($result->isActionable()) {
                $this->fixUpClassMethod($classMethod, $result, 'Illuminate\Support\Facades\Bus');
            }

            $result = $this->expectedClassMethodAnalyzer->findExpectedEventCallsWithClassMethod($classMethod);

            if (! $result instanceof ExpectedClassMethodMethodCalls) {
                continue;
            }

            if ($result->isActionable()) {
                $this->fixUpClassMethod($classMethod, $result, 'Illuminate\Support\Facades\Event');
            }
        }

        return $node;
    }

    private function fixUpClassMethod(
        ClassMethod $node,
        ExpectedClassMethodMethodCalls $result,
        string $facade
    ): ClassMethod
    {
        $this->removeAndReplaceMethodCalls($node, $result->getAllMethodCalls(), $result->getItemsToFake(), $facade);
        $node->stmts = array_merge(
            $node->stmts,
            $this->dispatchableTestsMethodsFactory->assertStatements($result->getExpectedItems(), $facade),
            $this->dispatchableTestsMethodsFactory->assertNotStatements($result->getNotExpectedItems(), $facade)
        );

        return $node;
    }

    private function removeAndReplaceMethodCalls(ClassMethod $node, array $expectedMethodCalls, array $classes, string $facade): ClassMethod
    {
        $first = true;
        $this->traverseNodesWithCallable($node, function (Node $node) use (&$first, $expectedMethodCalls, $classes, $facade): StaticCall|int|null {
            $match = false;
            foreach ($expectedMethodCalls as $methodCall) {
                if ($this->nodeComparator->areNodesEqual($node, $methodCall)) {
                    $match = true;
                    break;
                }
            }

            if ($match === false) {
                return null;
            }

            if ($first) {
                $first = false;
                return $this->dispatchableTestsMethodsFactory->makeFacadeFakeCall($classes, $facade);
            }

            return NodeVisitor::REMOVE_NODE;
        });

        return $node;
    }
}
