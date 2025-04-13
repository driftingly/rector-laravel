<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
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
    /**
     * @readonly
     */
    private ExpectedClassMethodAnalyzer $expectedClassMethodAnalyzer;
    /**
     * @readonly
     */
    private DispatchableTestsMethodsFactory $dispatchableTestsMethodsFactory;
    public function __construct(ExpectedClassMethodAnalyzer $expectedClassMethodAnalyzer, DispatchableTestsMethodsFactory $dispatchableTestsMethodsFactory)
    {
        $this->expectedClassMethodAnalyzer = $expectedClassMethodAnalyzer;
        $this->dispatchableTestsMethodsFactory = $dispatchableTestsMethodsFactory;
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
        $this->doesntExpectEvents(\App\Events\SomeOtherEvent::class);

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
        \Illuminate\Support\Facades\Event::fake([\App\Events\SomeEvent::class, \App\Events\SomeOtherEvent::class]);

        $this->get('/');

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeJob::class);
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeOtherJob::class);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SomeEvent::class);
        \Illuminate\Support\Facades\Event::assertNotDispatched(\App\Events\SomeOtherEvent::class);
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
        ClassMethod $classMethod,
        ExpectedClassMethodMethodCalls $expectedClassMethodMethodCalls,
        string $facade
    ): void {
        $this->removeAndReplaceMethodCalls($classMethod, $expectedClassMethodMethodCalls->getAllMethodCalls(), $expectedClassMethodMethodCalls->getItemsToFake(), $facade);

        $statements = array_merge($classMethod->stmts ?? [], $this->dispatchableTestsMethodsFactory->assertStatements($expectedClassMethodMethodCalls->getExpectedItems(), $facade), $this->dispatchableTestsMethodsFactory->assertNotStatements($expectedClassMethodMethodCalls->getNotExpectedItems(), $facade));

        $classMethod->stmts = $statements;

    }

    /**
     * @param  MethodCall[]  $expectedMethodCalls
     * @param  array<int<0, max>, ClassConstFetch|String_>  $classes
     */
    private function removeAndReplaceMethodCalls(ClassMethod $classMethod, array $expectedMethodCalls, array $classes, string $facade): void
    {
        $first = true;
        $this->traverseNodesWithCallable($classMethod, function (Node $node) use (&$first, $expectedMethodCalls, $classes, $facade) {
            $match = false;
            if (! $node instanceof Expression) {
                return null;
            }

            foreach ($expectedMethodCalls as $expectedMethodCall) {
                if ($this->nodeComparator->areNodesEqual($node->expr, $expectedMethodCall)) {
                    $match = true;
                    break;
                }
            }

            if ($match === false) {
                return null;
            }

            if ($first) {
                $first = false;

                return new Expression($this->dispatchableTestsMethodsFactory->makeFacadeFakeCall($classes, $facade));
            }

            return NodeVisitor::REMOVE_NODE;
        });

    }
}
