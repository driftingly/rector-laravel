<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Reflection\ReflectionProvider;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\RangeToCollectionRangeRectorTest;

/**
 * @see RangeToCollectionRangeRectorTest
 */
final class RangeToCollectionRangeRector extends AbstractRector
{
    private const string COLLECTION_CLASS = 'Illuminate\Support\Collection';

    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change `new Collection(range())` and `collect(range())` to `Collection::range()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

new Collection(range(1, 10));
collect(range(1, 10));
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

Collection::range(1, 10);
\Illuminate\Support\Collection::range(1, 10);
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class, FuncCall::class];
    }

    /**
     * @param  New_|FuncCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        $class = $this->resolveCollectionClass($node);
        if (! $class instanceof Name) {
            return null;
        }

        $rangeArgs = $this->resolveRangeArgs($node);
        if ($rangeArgs === null) {
            return null;
        }

        if (count($rangeArgs) > $this->collectionRangeParameterCount()) {
            return null;
        }

        return new StaticCall($class, 'range', $rangeArgs);
    }

    private function resolveCollectionClass(New_|FuncCall $node): ?Name
    {
        if ($node instanceof New_) {
            return $node->class instanceof Name && $this->isName($node->class, self::COLLECTION_CLASS)
                ? $node->class
                : null;
        }

        return $this->isName($node, 'collect')
            ? new FullyQualified(self::COLLECTION_CLASS)
            : null;
    }

    /**
     * @return array<Arg>|null
     */
    private function resolveRangeArgs(CallLike $callLike): ?array
    {
        $args = $this->resolvePositionalArgs($callLike);
        if ($args === null || count($args) !== 1) {
            return null;
        }

        $rangeCall = $args[0]->value;
        if (! $rangeCall instanceof FuncCall || ! $this->isName($rangeCall, 'range')) {
            return null;
        }

        $rangeArgs = $this->resolvePositionalArgs($rangeCall);
        if ($rangeArgs === null || count($rangeArgs) < 2 || count($rangeArgs) > 3) {
            return null;
        }

        foreach ($rangeArgs as $rangeArg) {
            if (! $this->getType($rangeArg->value)->isInteger()->yes()) {
                return null;
            }
        }

        return $rangeArgs;
    }

    /**
     * @return array<Arg>|null
     */
    private function resolvePositionalArgs(CallLike $callLike): ?array
    {
        if ($callLike->isFirstClassCallable()) {
            return null;
        }

        $args = $callLike->getArgs();
        foreach ($args as $arg) {
            if ($arg->unpack || $arg->name !== null) {
                return null;
            }
        }

        return $args;
    }

    private function collectionRangeParameterCount(): int
    {
        if (! $this->reflectionProvider->hasClass(self::COLLECTION_CLASS)) {
            return 0;
        }

        $classReflection = $this->reflectionProvider->getClass(self::COLLECTION_CLASS);
        if (! $classReflection->hasNativeMethod('range')) {
            return 0;
        }

        return count($classReflection->getNativeMethod('range')->getOnlyVariant()->getParameters());
    }
}
