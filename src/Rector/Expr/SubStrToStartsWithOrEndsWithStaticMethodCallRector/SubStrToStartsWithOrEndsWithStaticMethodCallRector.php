<?php

namespace RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRectorTest
 */
class SubStrToStartsWithOrEndsWithStaticMethodCallRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use Str::startsWith() or Str::endsWith() instead of substr() === $str', [
            new CodeSample(
                <<<'CODE_SAMPLE'
if (substr($str, 0, 3) === 'foo') {
    // do something
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
if (Str::startsWith($str, 'foo')) {
    // do something
}
CODE_SAMPLE,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Expr::class];
    }

    /**
     * @param Expr $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (!$node instanceof Expr\BinaryOp\Identical && !$node instanceof Expr\BinaryOp\Equal) {
            return null;
        }

        $functionCall = array_filter([$node->left, $node->right], function ($node) {
            return $node instanceof Expr\FuncCall && $this->isName($node, 'substr');
        });

        if ($functionCall === []) {
            return null;
        }

        $functionCall = reset($functionCall);

        $otherNode = array_filter([$node->left, $node->right], static function ($node) use ($functionCall) {
            return $node !== $functionCall;
        });

        $otherNode = reset($otherNode);

        // get the function call second argument value
        if (count($functionCall->args) < 2) {
            return null;
        }

        $secondArgument = $this->valueResolver->getValue($functionCall->args[1]->value);

        if (!is_int($secondArgument)) {
            return null;
        }

        if ($secondArgument < 0 && isset($functionCall->args[2])) {
            return null;
        }

        if (!$methodName = $this->getStaticMethodName($secondArgument)) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Str', $methodName, [
            $functionCall->args[0]->value,
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
