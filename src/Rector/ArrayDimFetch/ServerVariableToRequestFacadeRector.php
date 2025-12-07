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
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\ServerVariableToRequestFacadeRectorTest
 */
class ServerVariableToRequestFacadeRector extends AbstractRector
{
    private const string IS_IN_SERVER_VARIABLE = 'is_in_server_variable';

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
        return [Node::class, ArrayDimFetch::class];
    }

    public function refactor(Node $node): ?StaticCall
    {
        if (! $node instanceof ArrayDimFetch) {
            $this->traverseNodesWithCallable($node, function (Node $subNode) {
                if (in_array($subNode::class, [Assign::class, Isset_::class, Unset_::class, InterpolatedString::class], true)
                        && (! $subNode instanceof Assign || $subNode->var instanceof ArrayDimFetch && $this->isName($subNode->var->var, '_SERVER'))) {
                    $this->traverseNodesWithCallable($subNode, function (Node $subSubNode) {
                        if (! $subSubNode instanceof ArrayDimFetch) {
                            return null;
                        }

                        $subSubNode->setAttribute(self::IS_IN_SERVER_VARIABLE, true);

                        return $subSubNode;
                    });

                    return $subNode;
                }

                return null;
            });

            return null;
        }

        if (! $this->isName($node->var, '_SERVER')) {
            return null;
        }

        if (! $node->dim instanceof Expr) {
            return null;
        }

        if ($node->getAttribute(self::IS_IN_SERVER_VARIABLE) === true) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Request', 'server', [
            new Arg($node->dim),
        ]);
    }
}
