<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\FacadeAssertionAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\AssertWithClassStringToTypeHintedClosureRectorTest
 */
final class AssertWithClassStringToTypeHintedClosureRector extends AbstractRector
{
    public function __construct(
        private readonly FacadeAssertionAnalyzer $facadeAssertionAnalyzer
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes assert calls to use a type hinted closure.',
            [new CodeSample(
                <<<'CODE_SAMPLE'
Bus::assertDispatched(OrderCreated::class, function ($job) {
    return true;
});
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
Bus::assertDispatched(function (OrderCreated $job) {
    return true;
});
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->facadeAssertionAnalyzer->isFacadeAssertion($node)) {
            return null;
        }

        if ($node->isFirstClassCallable()) {
            return null;
        }

        if (count($node->getArgs()) !== 2) {
            return null;
        }

        if (! $node->args[1] instanceof Arg) {
            return null;
        }

        $functionLike = $node->args[1]->value;

        if (
            (! $functionLike instanceof Closure
            && ! $functionLike instanceof ArrowFunction)
            || ! isset($functionLike->params[0])
        ) {
            return null;
        }

        if (! $node->args[0] instanceof Arg) {
            return null;
        }

        $type = $this->getType($node->args[0]->value);

        $classString = match (true) {
            /** @phpstan-ignore method.notFound */
            $type->isClassString()->yes() => $type->getClassStringObjectType()->getClassName(),
            $type->isString()->yes()
                /** @phpstan-ignore method.notFound */
                && $type->getClassStringObjectType()->isObject()->yes() => $type->getClassStringObjectType()->getClassName(),
            default => null,
        };

        if (! is_string($classString)) {
            return null;
        }

        return $this->refactorClosure($node, $functionLike, $classString);
    }

    public function refactorClosure(StaticCall $staticCall, Closure|ArrowFunction $closure, string $class): StaticCall
    {
        $closure->params[0]->type = new FullyQualified($class);

        $staticCall->args = [
            new Arg($closure),
        ];

        return $staticCall;
    }
}
