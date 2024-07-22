<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\AvoidNegatedCollectionFilterOrRejectRector
 */
final class AvoidNegatedCollectionFilterOrRejectRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Avoid negated conditionals in `filter()` by using `reject()`, or vice versa',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => ! is_null($number))
    ->reject(fn (int $number): bool => ! $number > 0);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => is_null($number))
    ->filter(fn (int $number): bool => $number > 0);
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
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        return $this->updateFilterOrRejectCall($node);
    }

    private function updateFilterOrRejectCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Support\Collection'))) {
            return null;
        }

        if (! $this->isNames($methodCall->name, ['filter', 'reject'])) {
            return null;
        }

        $args = $methodCall->getArgs();
        if (count($args) !== 1) {
            return null;
        }

        $arg = $args[0];
        $argValue = $arg->value;

        if (! $argValue instanceof ArrowFunction) {
            return null;
        }

        $return = $argValue->expr;
        if (! $return instanceof BooleanNot) {
            return null;
        }

        $methodCall->name = new Identifier(
            $this->isName($methodCall->name, 'filter')
                ? 'reject'
                : 'filter'
        );

        $returnExpr = $return->expr;
        $argValue->expr = $this->getType($returnExpr)->isBoolean()->yes()
            ? $returnExpr
            : new Bool_($returnExpr);
        $argValue->returnType = new Identifier('bool');

        return $methodCall;
    }
}
