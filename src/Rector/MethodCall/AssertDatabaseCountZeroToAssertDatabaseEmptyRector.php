<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\AssertDatabaseCountZeroToAssertDatabaseEmptyRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AssertDatabaseCountZeroToAssertDatabaseEmptyRectorTest
 */
final class AssertDatabaseCountZeroToAssertDatabaseEmptyRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=9.39');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `assertDatabaseCount($table, 0)` with `assertDatabaseEmpty($table)`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testFoo()
    {
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('users', 0, 'other');
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testFoo()
    {
        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('users', 'other');
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'assertDatabaseCount')) {
            return null;
        }

        // the concern, not the TestCase, so tests that only pull the trait in are covered too
        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase'))) {
            return null;
        }

        if (count($node->getRawArgs()) > 3) {
            return null;
        }

        // getArg() resolves named or positional args, and returns null for first class callables
        $tableArg = $node->getArg('table', 0);
        $countArg = $node->getArg('count', 1);

        if (! $tableArg instanceof Arg || ! $countArg instanceof Arg) {
            return null;
        }

        // a union such as 0|1 resolves 0 as its first value, so the whole set must be exactly zero
        if ($this->getType($countArg->value)->getConstantScalarValues() !== [0]) {
            return null;
        }

        $connectionArg = $node->getArg('connection', 2);

        // both signatures name these params the same way, so named args carry over untouched
        $node->name = new Identifier('assertDatabaseEmpty');
        $node->args = $connectionArg instanceof Arg ? [$tableArg, $connectionArg] : [$tableArg];

        return $node;
    }
}
