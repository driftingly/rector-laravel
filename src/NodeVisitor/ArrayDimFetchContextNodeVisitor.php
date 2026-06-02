<?php

declare(strict_types=1);

namespace RectorLaravel\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitorAbstract;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;

final class ArrayDimFetchContextNodeVisitor extends NodeVisitorAbstract implements DecoratingNodeVisitorInterface
{
    public const string IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_SCALAR = 'is_inside_array_dim_fetch_with_dim_not_scalar';
    public const string IS_IN_SUPERGLOBAL_ASSIGN = 'is_in_superglobal_assign';
    public const string IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_EXPR = 'is_inside_array_dim_fetch_with_dim_not_expr';

    private const array SUPERGLOBAL_NAMES = ['_SERVER', '_GET', '_POST', '_REQUEST', '_ENV'];

    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver
    ) {}

    public function enterNode(Node $node)
    {
        if (! $node instanceof ArrayDimFetch) {
            if (in_array($node::class, [Assign::class, Isset_::class, Unset_::class, InterpolatedString::class], true)
                && (! $node instanceof Assign || $this->isSuperglobalAssign($node))) {
                SimpleCallableNodeTraverser::traverseNodesWithCallable($node, function (Node $subNode) {
                    if ($subNode instanceof ArrayDimFetch || $subNode instanceof Variable) {
                        $subNode->setAttribute(self::IS_IN_SUPERGLOBAL_ASSIGN, true);
                    }

                    return null;
                });
            }

            return null;
        }

        if (! $node->dim instanceof Expr) {
            SimpleCallableNodeTraverser::traverseNodesWithCallable($node, function (Node $subNode) {
                if (! $subNode instanceof Variable) {
                    return null;
                }

                $subNode->setAttribute(self::IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_EXPR, true);

                return $subNode;
            });
        }

        if ($node->dim instanceof Scalar) {
            return null;
        }

        SimpleCallableNodeTraverser::traverseNodesWithCallable($node, function (Node $subSubNode) {
            if ($subSubNode instanceof Variable) {
                $subSubNode->setAttribute(self::IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_SCALAR, true);

                return $subSubNode;
            }

            return null;
        });

        return null;
    }

    private function isSuperglobalAssign(Node $node): bool
    {
        if (! $node instanceof Assign) {
            return false;
        }

        if ($node->var instanceof ArrayDimFetch && $node->var->var instanceof Variable) {
            return $this->nodeNameResolver->isNames($node->var->var, self::SUPERGLOBAL_NAMES);
        }

        if ($node->var instanceof Variable) {
            return $this->nodeNameResolver->isNames($node->var, self::SUPERGLOBAL_NAMES);
        }

        return false;
    }
}
