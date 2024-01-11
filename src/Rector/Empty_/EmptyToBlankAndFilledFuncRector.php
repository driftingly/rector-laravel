<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Empty_;

use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Empty_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Empty_\EmptyToBlankAndFilledFuncRector\EmptyToBlankAndFilledFuncRectorTest
 */
class EmptyToBlankAndFilledFuncRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace use of the unsafe empty() function with Laravel\'s safer blank() & filled() functions.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
empty([]);
!empty([]);
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
        return [Empty_::class, BooleanNot::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof BooleanNot) {
            if (! $node->expr instanceof Empty_) {
                return null;
            }
            $method = 'filled';
            $args = [$node->expr->expr];
        } elseif ($node instanceof Empty_) {
            $method = 'blank';
            $args = [$node->expr];
        } else {
            return null;
        }

        return $this->nodeFactory->createFuncCall($method, $args);
    }
}
