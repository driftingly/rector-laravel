<?php

declare(strict_types=1);

namespace RectorLaravel\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\List_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;

final class ArrayDimFetchContextNodeVisitor extends NodeVisitorAbstract implements DecoratingNodeVisitorInterface
{
    public const string IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_SCALAR = 'is_inside_array_dim_fetch_with_dim_not_scalar';
    public const string IS_IN_SUPERGLOBAL_ASSIGN = 'is_in_superglobal_assign';
    public const string IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_EXPR = 'is_inside_array_dim_fetch_with_dim_not_expr';

    /**
     * Marks nodes that PHP only accepts as a variable, so replacing them with a function or
     * method call would be a fatal error: assignment targets (including compound, reference,
     * destructuring and nested dimensions), increment/decrement, foreach targets, by-reference
     * arguments, and the operands of isset()/unset().
     */
    public const string IS_IN_WRITE_CONTEXT = 'is_in_write_context';

    /**
     * Marks nodes inside a "$string {$interpolation}", where a call cannot be written either.
     */
    public const string IS_IN_INTERPOLATED_STRING = 'is_in_interpolated_string';

    private const array SUPERGLOBAL_NAMES = ['_SERVER', '_GET', '_POST', '_REQUEST', '_ENV'];

    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function enterNode(Node $node)
    {
        $this->markWriteContext($node);

        if (! $node instanceof ArrayDimFetch) {
            if (in_array($node::class, [Assign::class, Isset_::class, Unset_::class, InterpolatedString::class], true)
                && (! $node instanceof Assign || $this->isSuperglobalAssign($node))) {
                SimpleCallableNodeTraverser::traverseNodesWithCallable($node, static function (Node $subNode) {
                    if ($subNode instanceof ArrayDimFetch || $subNode instanceof Variable) {
                        $subNode->setAttribute(self::IS_IN_SUPERGLOBAL_ASSIGN, true);
                    }

                    return null;
                });
            }

            return null;
        }

        if (! $node->dim instanceof Expr) {
            SimpleCallableNodeTraverser::traverseNodesWithCallable($node, static function (Node $subNode) {
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

        SimpleCallableNodeTraverser::traverseNodesWithCallable($node, static function (Node $subSubNode) {
            if ($subSubNode instanceof Variable) {
                $subSubNode->setAttribute(self::IS_INSIDE_ARRAY_DIM_FETCH_WITH_DIM_NOT_SCALAR, true);

                return $subSubNode;
            }

            return null;
        });

        return null;
    }

    private function markWriteContext(Node $node): void
    {
        if ($node instanceof InterpolatedString) {
            SimpleCallableNodeTraverser::traverseNodesWithCallable($node, static function (Node $subNode) {
                if ($subNode instanceof ArrayDimFetch || $subNode instanceof Variable) {
                    $subNode->setAttribute(self::IS_IN_INTERPOLATED_STRING, true);
                }

                return null;
            });

            return;
        }

        if ($node instanceof Assign || $node instanceof AssignOp) {
            $this->markWriteTarget($node->var);

            return;
        }

        if ($node instanceof AssignRef) {
            $this->markWriteTarget($node->var);
            $this->markWriteTarget($node->expr);

            return;
        }

        if ($node instanceof PreInc || $node instanceof PostInc || $node instanceof PreDec || $node instanceof PostDec) {
            $this->markWriteTarget($node->var);

            return;
        }

        if ($node instanceof Foreach_) {
            if ($node->keyVar instanceof Expr) {
                $this->markWriteTarget($node->keyVar);
            }

            $this->markWriteTarget($node->valueVar);

            return;
        }

        if ($node instanceof Isset_ || $node instanceof Unset_) {
            foreach ($node->vars as $var) {
                $this->markWriteTarget($var);
            }

            return;
        }

        if ($node instanceof FuncCall) {
            $this->markByRefArgs($node);
        }
    }

    /**
     * Marks the variable chain of a write target, e.g. both $_ENV['a']['b'] and $_ENV['a'] in
     * $_ENV['a']['b'] = 1, while leaving the dimensions themselves - which stay reads - alone.
     */
    private function markWriteTarget(Expr $expr): void
    {
        if ($expr instanceof List_ || $expr instanceof Array_) {
            foreach ($expr->items as $item) {
                if ($item instanceof ArrayItem) {
                    $this->markWriteTarget($item->value);
                }
            }

            return;
        }

        while ($expr instanceof ArrayDimFetch) {
            $expr->setAttribute(self::IS_IN_WRITE_CONTEXT, true);
            $expr = $expr->var;
        }

        if ($expr instanceof Variable) {
            $expr->setAttribute(self::IS_IN_WRITE_CONTEXT, true);
        }
    }

    private function markByRefArgs(FuncCall $funcCall): void
    {
        if ($funcCall->isFirstClassCallable()) {
            return;
        }

        if (! $funcCall->name instanceof Name) {
            return;
        }

        if (! $this->reflectionProvider->hasFunction($funcCall->name, null)) {
            return;
        }

        $parameters = ParametersAcceptorSelector::combineAcceptors(
            $this->reflectionProvider->getFunction($funcCall->name, null)->getVariants()
        )->getParameters();

        $lastParameter = $parameters === [] ? null : $parameters[count($parameters) - 1];

        foreach ($funcCall->getArgs() as $position => $arg) {
            $parameter = $arg->name instanceof Identifier
                ? $this->findParameterByName($parameters, $arg->name->toString())
                : ($parameters[$position] ?? ($lastParameter?->isVariadic() === true ? $lastParameter : null));

            if ($parameter === null) {
                continue;
            }

            if (! $parameter->passedByReference()->yes()) {
                continue;
            }

            $this->markWriteTarget($arg->value);
        }
    }

    /**
     * @param  ParameterReflection[]  $parameters
     */
    private function findParameterByName(array $parameters, string $name): ?ParameterReflection
    {
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $name) {
                return $parameter;
            }
        }

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
