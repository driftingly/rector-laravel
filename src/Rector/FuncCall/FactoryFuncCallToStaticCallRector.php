<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\FuncCall;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\FactoryFuncCallToStaticCallRectorTest
 */
class FactoryFuncCallToStaticCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use the static factory method instead of global factory function.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
factory(User::class);
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
User::factory();
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    /**
     * @param Node\Expr\FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'factory')) {
            return null;
        }
        if (count($node->args) === 0) {
            return null;
        }
        $firstArgValue = $node->args[0]->value;
        if (! $firstArgValue instanceof Node\Expr\ClassConstFetch) {
            return null;
        }
        $model = $firstArgValue->class;

        if (count($node->args) === 1) {
            return new Node\Expr\StaticCall($model, 'factory');
        }
        return new Node\Expr\StaticCall($model, 'factory', [$node->args[1]]);
    }
}
