<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
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
            'Replace `$app->environment() === \'local\'` with `$app->environment(\'local\')`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$app->environment() === 'production';
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$app->environment('production');
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Expr::class];
    }

    public function refactor(Node $node): MethodCall|StaticCall|null
    {
        if (! $node instanceof Identical && ! $node instanceof Equal) {
            return null;
        }

        /** @var MethodCall|StaticCall|null $methodCall */
        $methodCall = array_values(
            array_filter(
                [$node->left, $node->right],
                fn ($node) => ($node instanceof MethodCall || $node instanceof StaticCall) && $this->isName(
                    $node->name,
                    'environment'
                )
            )
        )[0] ?? null;

        if ($methodCall === null || ! $this->validMethodCall($methodCall)) {
            return null;
        }

        /** @var Expr $otherNode */
        $otherNode = array_values(
            array_filter([$node->left, $node->right], static fn ($node) => $node !== $methodCall)
        )[0] ?? null;

        if (! $otherNode instanceof String_) {
            return null;
        }

        // make sure the method call has no arguments
        if ($methodCall->getArgs() !== []) {
            return null;
        }

        $methodCall->args[] = new Arg($otherNode);

        return $methodCall;
    }

    private function validMethodCall(MethodCall|StaticCall $methodCall): bool
    {
        return match (true) {
            $methodCall instanceof MethodCall && $this->isObjectType(
                $methodCall->var,
                new ObjectType('Illuminate\Contracts\Foundation\Application')
            ) => true,
            $methodCall instanceof StaticCall && $this->isObjectType(
                $methodCall->class,
                new ObjectType('Illuminate\Support\Facades\App')
            ) => true,
            $methodCall instanceof StaticCall && $this->isObjectType(
                $methodCall->class,
                new ObjectType('App')
            ) => true,
            default => false,
        };
    }
}
