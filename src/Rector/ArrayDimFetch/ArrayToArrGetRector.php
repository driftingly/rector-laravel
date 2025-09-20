<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name\FullyQualified;
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
    /**
     * @var ArrayDimFetch[]
     */
    private array $processedArrayDimFetches = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert array access to Arr::get() method call, skips null coalesce with throw expressions',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$array['key'];
$array['nested']['key'];
$array['key'] ?? 'default';
$array['nested']['key'] ?? 'default';
$array['key'] ?? throw new Exception('Required');
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Arr::get($array, 'key');
\Illuminate\Support\Arr::get($array, 'nested.key');
\Illuminate\Support\Arr::get($array, 'key', 'default');
\Illuminate\Support\Arr::get($array, 'nested.key', 'default');
$array['key'] ?? throw new Exception('Required');
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class, Coalesce::class];
    }

    /**
     * @param  ArrayDimFetch|Coalesce  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if ($node instanceof Coalesce) {
            $result = $this->refactorCoalesce($node);
            if ($result instanceof StaticCall && $node->left instanceof ArrayDimFetch) {
                $this->processedArrayDimFetches[] = $node->left;
            }

            return $result;
        }

        if ($node instanceof ArrayDimFetch) {
            if (in_array($node, $this->processedArrayDimFetches, true)) {
                return null;
            }

            return $this->refactorArrayDimFetch($node);
        }

        return null;
    }

    private function refactorCoalesce(Coalesce $coalesce): ?StaticCall
    {
        if (! $coalesce->left instanceof ArrayDimFetch) {
            return null;
        }

        if ($coalesce->right instanceof Throw_) {
            $this->markArrayDimFetchAsProcessed($coalesce->left);

            return null;
        }

        $staticCall = $this->createArrGetCall($coalesce->left);
        if (! $staticCall instanceof StaticCall) {
            return null;
        }

        $staticCall->args[] = new Arg($coalesce->right);

        return $staticCall;
    }

    private function refactorArrayDimFetch(ArrayDimFetch $arrayDimFetch): ?StaticCall
    {
        return $this->createArrGetCall($arrayDimFetch);
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

    private function markArrayDimFetchAsProcessed(ArrayDimFetch $arrayDimFetch): void
    {
        $this->processedArrayDimFetches[] = $arrayDimFetch;

        $current = $arrayDimFetch;
        while ($current instanceof ArrayDimFetch) {
            $this->processedArrayDimFetches[] = $current;
            $current = $current->var;
        }
    }
}
