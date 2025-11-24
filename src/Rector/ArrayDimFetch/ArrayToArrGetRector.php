<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitor;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\ArrayToArrGetRectorTest
 */
final class ArrayToArrGetRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert array access to Arr::get() method call, skips isset/empty checks, assignments, unset, and null coalesce with throw expressions',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$array['key'];
$array['nested']['key'];
$array['key'] ?? 'default';
$array['nested']['key'] ?? 'default';
$array['key'] ?? throw new Exception('Required');
isset($array['key']);
empty($array['key']);
$array['key'] = 'value';
unset($array['key']);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Arr::get($array, 'key');
\Illuminate\Support\Arr::get($array, 'nested.key');
\Illuminate\Support\Arr::get($array, 'key', 'default');
\Illuminate\Support\Arr::get($array, 'nested.key', 'default');
$array['key'] ?? throw new Exception('Required');
isset($array['key']);
empty($array['key']);
$array['key'] = 'value';
unset($array['key']);
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
            ArrayDimFetch::class,
            Coalesce::class,
            Isset_::class,
            Empty_::class,
            Assign::class,
            AssignOp::class,
            Unset_::class,
        ];
    }

    /**
     * @param  ArrayDimFetch|Coalesce|Isset_|Empty_|Assign|AssignOp|Unset_  $node
     * @return StaticCall|NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN|null
     */
    public function refactor(Node $node): StaticCall|int|null
    {
        if ($node instanceof Coalesce) {
            return $this->refactorCoalesce($node);
        }

        if ($node instanceof AssignOp || $node instanceof Assign) {
            if ($this->containsArrayDimFetch($node->var)) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            return null;
        }

        if (! $node instanceof ArrayDimFetch) {
            if ($this->containsArrayDimFetch($node)) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            return null;
        }

        return $this->createArrGetCall($node);
    }

    private function refactorCoalesce(Coalesce $coalesce): StaticCall|int
    {
        if (! $coalesce->left instanceof ArrayDimFetch) {
            return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        if ($coalesce->right instanceof Throw_) {
            return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        $staticCall = $this->createArrGetCall($coalesce->left);
        if (! $staticCall instanceof StaticCall) {
            return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        $staticCall->args[] = new Arg($coalesce->right);

        return $staticCall;
    }

    private function createArrGetCall(ArrayDimFetch $arrayDimFetch): ?StaticCall
    {
        if (! $this->isValidArrayDimFetch($arrayDimFetch)) {
            return null;
        }

        $keyPath = $this->buildKeyPath($arrayDimFetch);
        if (! $keyPath instanceof Expr) {
            return null;
        }

        $expr = $this->getRootVariable($arrayDimFetch);

        return new StaticCall(
            new FullyQualified('Illuminate\Support\Arr'),
            'get',
            [
                new Arg($expr),
                new Arg($keyPath),
            ]
        );
    }

    private function isValidArrayDimFetch(ArrayDimFetch $arrayDimFetch): bool
    {
        return $arrayDimFetch->dim instanceof Scalar;
    }

    private function buildKeyPath(ArrayDimFetch $arrayDimFetch): ?Expr
    {
        $keys = [];
        $current = $arrayDimFetch;

        while ($current instanceof ArrayDimFetch) {
            if (! $this->isValidArrayDimFetch($current)) {
                return null;
            }

            /** @var scalar $dim */
            $dim = $current->dim;
            array_unshift($keys, $dim);
            $current = $current->var;
        }

        if (count($keys) === 0) {
            return null;
        }

        if (count($keys) === 1) {
            return $keys[0];
        }

        return $this->createDotNotationString($keys);
    }

    /**
     * @param  array<scalar>  $keys
     */
    private function createDotNotationString(array $keys): ?String_
    {
        $stringParts = [];

        foreach ($keys as $key) {
            if (! $key instanceof Scalar) {
                return null;
            }

            $constantValues = $this->getType($key)->getConstantScalarValues();

            if ($constantValues === []) {
                return null;
            }

            $value = $constantValues[0];

            if (! is_string($value) && ! is_int($value)) {
                return null;
            }

            $stringParts[] = (string) $value;
        }

        return new String_(implode('.', $stringParts));
    }

    private function getRootVariable(ArrayDimFetch $arrayDimFetch): Expr
    {
        $current = $arrayDimFetch;

        while ($current instanceof ArrayDimFetch) {
            $current = $current->var;
        }

        return $current;
    }

    private function containsArrayDimFetch(Assign|Unset_|Node|Coalesce|ArrayDimFetch|Isset_|Empty_|AssignOp $node): bool
    {
        $found = false;

        $this->traverseNodesWithCallable($node, function (Node $node) use (&$found): ?int {
            if ($node instanceof ArrayDimFetch) {
                $found = true;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $found;
    }
}
