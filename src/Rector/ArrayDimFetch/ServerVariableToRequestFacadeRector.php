<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\ServerVariableToRequestFacadeRectorTest
 */
class ServerVariableToRequestFacadeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change server variable to Request facade\'s server method',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$_SERVER['VARIABLE'];
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facade\Request::server('VARIABLE');
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param  ArrayDimFetch  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->isName($node->var, '_SERVER')) {
            return null;
        }

        if ($node->dim === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Request', 'server', [
            new Arg($node->dim),
        ]);
    }
}
