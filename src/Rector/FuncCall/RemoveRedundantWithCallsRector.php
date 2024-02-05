<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use Rector\Rector\AbstractScopeAwareRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\RemoveRedundantWithCallsRectorTest
 */
class RemoveRedundantWithCallsRector extends AbstractScopeAwareRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes redundant with helper calls', [
            new CodeSample(
                'with(new Object())->something();',
                '(new Object())->something();'
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if (! $node->name instanceof Name) {
            return null;
        }

        if (! $this->isName($node->name, 'with')) {
            return null;
        }

        $args = $node->getArgs();

        if (count($args) < 1 || count($args) > 2) {
            return null;
        }

        if (count($args) === 2) {
            $secondArgumentType = $scope->getType($args[1]->value);

            if ($secondArgumentType->isCallable()->no() === false) {
                return null;
            }
        }

        return $args[0]->value;
    }
}
