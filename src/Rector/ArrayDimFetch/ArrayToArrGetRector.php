<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
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
            'Convert array access to Arr::get() method call',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$array['key'];
$array['nested']['key'];
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Arr::get($array, 'key');
\Illuminate\Support\Arr::get($array, 'nested.key');
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param  ArrayDimFetch  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if ($node->dim === null) {
            return null;
        }

        if (! $node->dim instanceof Scalar) {
            return null;
        }

        $keyPath = $this->buildKeyPath($node);
        if (! $keyPath instanceof Expr) {
            return null;
        }

        $expr = $this->getRootVariable($node);

        return new StaticCall(
            new FullyQualified('Illuminate\Support\Arr'),
            'get',
            [
                new Arg($expr),
                new Arg($keyPath),
            ]
        );
    }

    private function buildKeyPath(ArrayDimFetch $arrayDimFetch): ?Expr
    {
        $keys = [];
        $current = $arrayDimFetch;

        while ($current instanceof ArrayDimFetch) {
            if (! $current->dim instanceof Expr || ! $current->dim instanceof Scalar) {
                return null;
            }

            array_unshift($keys, $current->dim);
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
     * @param  array<Scalar>  $keys
     */
    private function createDotNotationString(array $keys): ?String_
    {
        $stringParts = [];

        foreach ($keys as $key) {
            $value = $this->getType($key)->getConstantScalarValues()[0] ?? null;

            if ($value === null) {
                return null;
            }

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
}
