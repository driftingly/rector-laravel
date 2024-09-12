<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\ReplaceExpectsMethodsInTestsRectorTest
 */
class ReplaceExpectsMethodsInTestsRector extends AbstractRector
{
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

        // loop over all methods in class
        foreach ($node->getMethods() as $classMethod) {
            // loop over all statements in method

            $assertions = [];
            foreach ($classMethod->getStmts() ?? [] as $index => $stmt) {
                // if statement is not a method call, skip
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof MethodCall) {
                    continue;
                }

                $methodCall = $stmt->expr;

                // if method call is not expectsJobs or expectsEvents, skip
                if (! $this->isNames($methodCall->name, ['expectsJobs', 'expectsEvents'])) {
                    continue;
                }

                // if method call is not in the form $this->expectsJobs(...), skip
                if (! $methodCall->var instanceof Variable || ! $this->isName($methodCall->var, 'this')) {
                    continue;
                }

                if ($methodCall->args === []) {
                    continue;
                }

                // if the method call has a string constant as the first argument,
                // convert it to an array
                if ($methodCall->args[0] instanceof Arg && (
                    $methodCall->args[0]->value instanceof ClassConstFetch ||
                    $methodCall->args[0]->value instanceof String_
                )) {
                    $args = new Array_([new ArrayItem($methodCall->args[0]->value)]);
                } elseif (
                    $methodCall->args[0] instanceof Arg &&
                    $methodCall->args[0]->value instanceof Array_
                ) {
                    $args = $methodCall->args[0]->value;
                } else {
                    continue;
                }

                if (! $methodCall->name instanceof Identifier) {
                    continue;
                }

                switch ($methodCall->name->name) {
                    case 'expectsJobs':
                        $facade = 'Bus';
                        break;
                    case 'expectsEvents':
                        $facade = 'Event';
                        break;
                    default:
                        $facade = null;
                        break;
                }

                if ($facade === null) {
                    continue;
                }

                $replacement = new Expression(new StaticCall(
                    new FullyQualified('Illuminate\Support\Facades\\' . $facade),
                    'fake',
                    [new Arg($args)]
                ));

                $classMethod->stmts[$index] = $replacement;

                // generate assertDispatched calls for each argument
                foreach ($args->items as $item) {
                    if ($item === null) {
                        continue;
                    }

                    $assertions[] = new Expression(new StaticCall(
                        new FullyQualified('Illuminate\Support\Facades\\' . $facade),
                        'assertDispatched',
                        [new Arg($item->value)]
                    ));
                }

                $changes = true;
            }

            foreach ($assertions as $assertion) {
                $classMethod->stmts[] = $assertion;
            }
        }

        return $changes ? $node : null;
    }
}
