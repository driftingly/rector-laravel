<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\AppToResolveRector\AppToResolveRectorTest
 */
class AppToResolveRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert app() to resolve() where applicable.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
app('foo');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
resolve('foo');
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?FuncCall
    {
        if (! $this->isName($node, 'app')) {
            return null;
        }

        $abstract = $node->getArg('abstract', 0);

        if ($abstract === null) {
            return null;
        }

        if ($this->getType($abstract->value)->isNull()->yes()) {
            return null;
        }

        $node->name = new Name('resolve');

        return $node;
    }
}
