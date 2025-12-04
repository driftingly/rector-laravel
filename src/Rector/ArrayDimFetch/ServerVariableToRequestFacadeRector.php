<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use Override;
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
    /**
     * @var string
     */
    private const IS_IN_SERVER_VARIABLE = 'is_in_server_variable';

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

    #[Override]
    public function beforeTraverse(array $nodes): array
    {
        parent::beforeTraverse($nodes);

        $this->traverseNodesWithCallable($nodes, function (Node $node) {
            if (in_array(get_class($node), [Assign::class, Isset_::class, Unset_::class, InterpolatedString::class], true)
                    && (! $node instanceof Assign || $node->var instanceof ArrayDimFetch && $this->isName($node->var->var, '_SERVER'))) {
                $this->traverseNodesWithCallable($node, function (Node $subNode) {
                    if (! $subNode instanceof ArrayDimFetch) {
                        return null;
                    }

                    $subNode->setAttribute(self::IS_IN_SERVER_VARIABLE, true);

                    return $subNode;
                });

                return $node;
            }

            return null;
        });

        return $nodes;
    }

    /**
     * @param  ArrayDimFetch  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
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
