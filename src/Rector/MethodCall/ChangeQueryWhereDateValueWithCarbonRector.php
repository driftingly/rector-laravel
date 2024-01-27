<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/25315
 * @changelog https://laracasts.com/discuss/channels/eloquent/laravel-eloquent-where-date-is-equal-or-smaller-than-datetime
 *
 * @see \RectorLaravel\Tests\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector\ChangeQueryWhereDateValueWithCarbonRectorTest
 */
final class ChangeQueryWhereDateValueWithCarbonRector extends AbstractRector
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
        return new RuleDefinition(
            'Add parent::boot(); call to boot() class method in child of Illuminate\Database\Eloquent\Model',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Query\Builder;

final class SomeClass
{
    public function run(Builder $query)
    {
        $query->whereDate('created_at', '<', Carbon::now());
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Query\Builder;

final class SomeClass
{
    public function run(Builder $query)
    {
        $dateTime = Carbon::now();
        $query->whereDate('created_at', '<=', $dateTime);
        $query->whereTime('created_at', '<=', $dateTime);
    }
}
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
        return [Expression::class];
    }

    /**
     * @param  Expression  $node
     * @return \PhpParser\Node|mixed[]|int|null
     */
    public function refactor(Node $node)
    {
        if (! $node->expr instanceof MethodCall) {
            return null;
        }

        $expr = $this->matchWhereDateThirdArgValue($node->expr);
        if (! $expr instanceof Expr) {
            return null;
        }

        // is just made with static call?
        if ($expr instanceof StaticCall || $expr instanceof MethodCall) {
            // now!
            // 1. extract assign
            $dateTimeVariable = new Variable('dateTime');
            $assign = new Assign($dateTimeVariable, $expr);

            $nodes = [new Expression($assign), $node];

            if (! $node->expr->args[2] instanceof Arg) {
                return $nodes;
            }

            $node->expr->args[2]->value = $dateTimeVariable;
            if (! $node->expr->args[1] instanceof Arg) {
                return $nodes;
            }

            // update assign ">" → ">="
            $this->changeCompareSignExpr($node->expr->args[1]);

            // 2. add "whereTime()" time call
            $whereTimeMethodCall = $this->createWhereTimeMethodCall($node->expr, $dateTimeVariable);

            $nodes[] = new Expression($whereTimeMethodCall);

            return $nodes;
        }

        if ($expr instanceof Variable && $node->expr->args[1] instanceof Arg) {
            $dateTimeVariable = $expr;

            $this->changeCompareSignExpr($node->expr->args[1]);

            // 2. add "whereTime()" time call
            $whereTimeMethodCall = $this->createWhereTimeMethodCall($node->expr, $dateTimeVariable);

            return [$node, new Expression($whereTimeMethodCall)];
        }

        return null;
    }

    private function matchWhereDateThirdArgValue(MethodCall $methodCall): ?Expr
    {
        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Database\Query\Builder'))) {
            return null;
        }

        if (! $this->isName($methodCall->name, 'whereDate')) {
            return null;
        }

        if (! isset($methodCall->args[2])) {
            return null;
        }

        if (! $methodCall->args[2] instanceof Arg) {
            return null;
        }

        $argValue = $methodCall->args[2]->value;
        if (! $this->isObjectType($argValue, new ObjectType('DateTimeInterface'))) {
            return null;
        }

        // nothing to change
        if ($this->isCarbonTodayStaticCall($argValue)) {
            return null;
        }

        if (! $methodCall->args[1] instanceof Arg) {
            return null;
        }

        if ($this->valueResolver->isValues($methodCall->args[1]->value, ['>=', '<='])) {
            return null;
        }

        return $argValue;
    }

    private function changeCompareSignExpr(Arg $arg): void
    {
        if (! $arg->value instanceof String_) {
            return;
        }

        $string = $arg->value;

        if ($string->value === '<') {
            $string->value = '<=';
        }

        if ($string->value === '>') {
            $string->value = '>=';
        }
    }

    private function createWhereTimeMethodCall(MethodCall $methodCall, Variable $dateTimeVariable): MethodCall
    {
        $whereTimeArgs = [$methodCall->args[0], $methodCall->args[1], new Arg($dateTimeVariable)];

        return new MethodCall($methodCall->var, 'whereTime', $whereTimeArgs);
    }

    private function isCarbonTodayStaticCall(Expr $expr): bool
    {
        if (! $expr instanceof StaticCall) {
            return false;
        }

        $carbonObjectType = new ObjectType('Carbon\Carbon');

        $callerType = $this->nodeTypeResolver->getType($expr->class);
        if (! $carbonObjectType->isSuperTypeOf($callerType)->yes()) {
            return false;
        }

        return $this->isName($expr->name, 'today');
    }
}
