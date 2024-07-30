<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRectorTest
 */
class SubStrToStartsWithOrEndsWithStaticMethodCallRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use Str::startsWith() or Str::endsWith() instead of substr() === $str', [
            new CodeSample(<<<'CODE_SAMPLE'
if (substr($str, 0, 3) === 'foo') {
    // do something
}
CODE_SAMPLE, <<<'CODE_SAMPLE'
if (Str::startsWith($str, 'foo')) {
    // do something
}
CODE_SAMPLE),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Expr::class];
    }

    /**
     * @param  Expr  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $node instanceof Identical && ! $node instanceof Equal) {
            return null;
        }

        /** @var Expr\FuncCall|null $functionCall */
        $functionCall = array_values(
            array_filter([$node->left, $node->right], function ($node) {
                return $node instanceof FuncCall && $this->isName(
                    $node,
                    'substr'
                );
            })
        )[0] ?? null;

        if (! $functionCall instanceof FuncCall) {
            return null;
        }

        /** @var Expr $otherNode */
        $otherNode = array_values(
            array_filter([$node->left, $node->right], static function ($node) use ($functionCall) {
                return $node !== $functionCall;
            })
        )[0] ?? null;

        // get the function call second argument value
        if (count($functionCall->getArgs()) < 2) {
            return null;
        }

        $secondArgument = $this->valueResolver->getValue($functionCall->getArgs()[1]->value);

        if (! is_int($secondArgument)) {
            return null;
        }

        if ($secondArgument < 0 && isset($functionCall->getArgs()[2])) {
            return null;
        }

        $methodName = $this->getStaticMethodName($secondArgument);

        if ($methodName === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Str', $methodName, [
            $functionCall->getArgs()[0]
->value,
            $otherNode,
        ]);
    }

    protected function getStaticMethodName(int $secondArgument): ?string
    {
        if ($secondArgument === 0) {
            return 'startsWith';
        }

        if ($secondArgument < 0) {
            return 'endsWith';
        }

        return null;
    }
}
