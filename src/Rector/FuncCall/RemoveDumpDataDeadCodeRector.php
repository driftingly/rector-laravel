<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\RemoveDumpDataDeadCodeRectorTest
 */
final class RemoveDumpDataDeadCodeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'It will removes the dump data just like dd or dump functions from the code.`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class MyController
{
    public function store()
    {
        dd('test');
        return true;
    }

    public function update()
    {
        dump('test');
        return true;
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class MyController
{
    public function store()
    {
        return true;
    }

    public function update()
    {
        return true;
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
        return [Expression::class];
    }

    /**
     * @param  Expression  $node
     * @return int|\PhpParser\Node|mixed[]|null
     */
    public function refactor(Node $node)
    {
        if (! $node->expr instanceof FuncCall) {
            return null;
        }

        if (! $this->isNames($node->expr->name, ['dd', 'dump'])) {
            return null;
        }

        return NodeTraverser::REMOVE_NODE;
    }
}
