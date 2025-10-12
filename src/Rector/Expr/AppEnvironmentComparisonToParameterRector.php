<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\AppEnvironmentComparisonToParameterRectorTest
 */
class AppEnvironmentComparisonToParameterRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace app environment comparison with parameter or method call',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$app->environment() === 'local';
$app->environment() !== 'production';
$app->environment() === 'testing';
in_array($app->environment(), ['local', 'testing']);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$app->isLocal();
! $app->isProduction();
$app->environment('testing');
$app->environment(['local', 'testing']);
CODE_SAMPLE
                ),
            ]
        );
    }

    /** @return list<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class, Identical::class, Equal::class, NotIdentical::class, NotEqual::class];
    }

    /** @param FuncCall|Identical|Equal|NotIdentical|NotEqual $node */
    public function refactor(Node $node): ?Expr
    {
        if ($node instanceof FuncCall) {
            [$methodCall, $otherNode] = $this->handleFuncCall($node);
        } else {
            [$methodCall, $otherNode] = $this->handleBinaryOp($node);
        }

        if ($methodCall === null || $otherNode === null) {
            return null;
        }

        $methodName = null;

        if ($otherNode instanceof String_) {
            $methodName = $this->getMethodName($methodCall, $otherNode->value);
        }

        if ($methodName === null) {
            $methodCall->args[] = new Arg($otherNode);
        } else {
            $methodCall->name = new Identifier($methodName);
        }

        if ($node instanceof NotIdentical || $node instanceof NotEqual) {
            return new BooleanNot($methodCall);
        }

        return $methodCall;
    }

    /** @return array{0: MethodCall|StaticCall|null, 1: Expr|null} */
    private function handleFuncCall(FuncCall $funcCall): array
    {
        if (! $this->isName($funcCall->name, 'in_array')) {
            return [null, null];
        }

        $methodCall = $funcCall->getArg('needle', 0);
        $haystack = $funcCall->getArg('haystack', 1);

        if (! $haystack instanceof Arg || ! $methodCall instanceof Arg || ! $this->validMethodCall($methodCall->value)) {
            return [null, null];
        }

        return [$methodCall->value, $haystack->value];
    }

    /** @return array{0: MethodCall|StaticCall|null, 1: Expr|null} */
    private function handleBinaryOp(BinaryOp $binaryOp): array
    {
        $methodCall = array_values(
            array_filter(
                [$binaryOp->left, $binaryOp->right],
                fn ($node) => $this->validMethodCall($node),
            )
        )[0] ?? null;

        $otherNode = array_values(
            array_filter([$binaryOp->left, $binaryOp->right], static fn ($node) => $node !== $methodCall)
        )[0] ?? null;

        return [$methodCall, $otherNode];
    }

    /** @phpstan-assert-if-true MethodCall|StaticCall $node */
    private function validMethodCall(Node $node): bool
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return false;
        }

        if (! $this->isName($node->name, 'environment')) {
            return false;
        }

        // make sure the method call has no arguments
        if ($node->getArgs() !== []) {
            return false;
        }

        switch (true) {
            case $node instanceof MethodCall && $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Contracts\Foundation\Application')
            ):
                return true;
            case $node instanceof StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Support\Facades\App')
            ):
                return true;
            case $node instanceof StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('App')
            ):
                return true;
            default:
                return false;
        }
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $methodCall
     */
    private function getMethodName($methodCall, string $environment): ?string
    {
        if (
            $methodCall instanceof MethodCall
            && ! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Foundation\Application'))
        ) {
            return null;
        }

        switch ($environment) {
            case 'local':
                return 'isLocal';
            case 'production':
                return 'isProduction';
            default:
                return null;
        }
    }
}
