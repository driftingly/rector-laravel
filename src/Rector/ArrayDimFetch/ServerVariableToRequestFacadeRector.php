<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitor;
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
        return [Assign::class, Isset_::class, Unset_::class, InterpolatedString::class, ArrayDimFetch::class];
    }

    /**
     * @param  ArrayDimFetch|Assign|Isset_|Unset_|InterpolatedString  $node
     * @return StaticCall|NodeVisitor::DONT_TRAVERSE_CHILDREN|null
     */
    public function refactor(Node $node)
    {
        if (! $node instanceof ArrayDimFetch) {
            if (! $node instanceof Assign) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if (! $node->var instanceof ArrayDimFetch || ! $this->isName($node->var->var, '_SERVER')) {
                return null;
            }

            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        if (! $this->isName($node->var, '_SERVER')) {
            return null;
        }

        if (! $node->dim instanceof Expr) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Request', 'server', [
            new Arg($node->dim),
        ]);
    }
}
