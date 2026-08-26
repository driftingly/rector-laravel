<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\AssertDatabaseCountZeroToAssertDatabaseEmptyRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AssertDatabaseCountZeroToAssertDatabaseEmptyRectorTest
 */
final class AssertDatabaseCountZeroToAssertDatabaseEmptyRector extends AbstractRector
{
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

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Foundation\Testing\TestCase'))) {
            return null;
        }

        $args = $node->getArgs();

        if (count($args) < 2 || count($args) > 3) {
            return null;
        }

        foreach ($args as $arg) {
            if ($arg->name !== null) {
                return null;
            }
        }

        // a union such as 0|1 resolves 0 as its first value, so the whole set must be exactly zero
        if ($this->getType($args[1]->value)->getConstantScalarValues() !== [0]) {
            return null;
        }

        $node->name = new Identifier('assertDatabaseEmpty');
        $node->args = isset($args[2]) ? [$args[0], $args[2]] : [$args[0]];

        return $node;
    }
}
