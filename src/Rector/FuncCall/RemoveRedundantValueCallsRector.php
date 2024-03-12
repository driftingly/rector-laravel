<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Type\ClosureType;
use PHPStan\Type\MixedType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\RemoveRedundantValueCallsRectorTest
 */
class RemoveRedundantValueCallsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes redundant value helper calls', [
            new CodeSample(
                'value(new Object())->something();',
                '(new Object())->something();'
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if (! $node->name instanceof Name) {
            return null;
        }

        if (! $this->isName($node->name, 'value')) {
            return null;
        }

        $args = $node->getArgs();

        if (count($args) !== 1) {
            return null;
        }

        if ($this->getType($args[0]->value)->isSuperTypeOf(new ClosureType([], new MixedType, true))->no() === false) {
            return null;
        }

        return $args[0]->value;
    }
}
