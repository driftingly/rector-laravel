<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector\NotFilledBlankFuncCallToBlankFilledFuncCallRectorTest
 */
class NotFilledBlankFuncCallToBlankFilledFuncCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Swap the use of NotBooleans used with filled() and blank() to the correct helper.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
!filled([]);
!blank([]);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
blank([]);
filled([]);
CODE_SAMPLE
                ),

            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [BooleanNot::class];
    }

    /**
     * @param  BooleanNot  $node
     */
    public function refactor(Node $node): ?FuncCall
    {
        if (! $node->expr instanceof FuncCall) {
            return null;
        }

        if (
            ! $this->isName($node->expr->name, 'filled') &&
            ! $this->isName($node->expr->name, 'blank')
        ) {
            return null;
        }

        $method = $this->isName($node->expr->name, 'filled') ? 'blank' : 'filled';

        return $this->nodeFactory->createFuncCall($method, $node->expr->args);
    }
}
