<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
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
 * @changelog https://github.com/laravel/framework/pull/54408
 *
 * @see \RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\DateWhereClauseToShorthandRectorTest
 */
final class DateWhereClauseToShorthandRector extends AbstractRector
{
    private const array WHERE_NOW_METHODS = [
        'where' => [
            '<' => 'wherePast',
            '<=' => 'whereNowOrPast',
            '>' => 'whereFuture',
            '>=' => 'whereNowOrFuture',
        ],
        'orwhere' => [
            '<' => 'orWherePast',
            '<=' => 'orWhereNowOrPast',
            '>' => 'orWhereFuture',
            '>=' => 'orWhereNowOrFuture',
        ],
    ];

    private const array WHERE_TODAY_METHODS = [
        'wheredate' => [
            '=' => 'whereToday',
            '<' => 'whereBeforeToday',
            '<=' => 'whereTodayOrBefore',
            '>' => 'whereAfterToday',
            '>=' => 'whereTodayOrAfter',
        ],
        'orwheredate' => [
            '=' => 'orWhereToday',
            '<' => 'orWhereBeforeToday',
            '<=' => 'orWhereTodayOrBefore',
            '>' => 'orWhereAfterToday',
            '>=' => 'orWhereTodayOrAfter',
        ],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace date comparison where clauses with Laravel query builder shorthand methods.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Carbon\Carbon;

$query->where('published_at', '<', Carbon::now());
$query->whereDate('published_at', '=', Carbon::today());
$query->where('published_at', '<=', now());
$query->whereDate('published_at', '>=', today());
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Carbon\Carbon;

$query->wherePast('published_at');
$query->whereToday('published_at');
$query->whereNowOrPast('published_at');
$query->whereTodayOrAfter('published_at');
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
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isSupportedCaller($node)) {
            return null;
        }

        $callName = strtolower((string) $this->getName($node->name));

        if (isset(self::WHERE_NOW_METHODS[$callName])) {
            return $this->refactorWhereNowCall($node, $callName);
        }

        if (isset(self::WHERE_TODAY_METHODS[$callName])) {
            return $this->refactorWhereTodayCall($node, $callName);
        }

        return null;
    }

    private function isSupportedCaller(MethodCall|StaticCall $node): bool
    {
        if ($node instanceof StaticCall) {
            return $this->isObjectType($node->class, new ObjectType('Illuminate\Database\Eloquent\Model'));
        }

        return $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Database\Query\Builder'))
            || $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Query\Builder'))
            || $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Eloquent\Builder'));
    }

    private function refactorWhereNowCall(MethodCall|StaticCall $node, string $callName): ?Node
    {
        if (count($node->args) !== 3) {
            return null;
        }

        $operator = $this->resolveStringArgumentValue($node->args[1] ?? null);
        if ($operator === null || ! isset(self::WHERE_NOW_METHODS[$callName][$operator])) {
            return null;
        }

        if (! isset($node->args[2]) || ! $node->args[2] instanceof Arg) {
            return null;
        }

        if (! $this->isNowExpression($node->args[2]->value)) {
            return null;
        }

        return $this->renameAndKeepFirstArgument($node, self::WHERE_NOW_METHODS[$callName][$operator]);
    }

    private function refactorWhereTodayCall(MethodCall|StaticCall $node, string $callName): ?Node
    {
        if (count($node->args) === 2) {
            if (! isset($node->args[1]) || ! $node->args[1] instanceof Arg) {
                return null;
            }

            if (! $this->isTodayExpression($node->args[1]->value)) {
                return null;
            }

            return $this->renameAndKeepFirstArgument($node, self::WHERE_TODAY_METHODS[$callName]['=']);
        }

        if (count($node->args) !== 3) {
            return null;
        }

        $operator = $this->resolveStringArgumentValue($node->args[1] ?? null);
        if ($operator === null || ! isset(self::WHERE_TODAY_METHODS[$callName][$operator])) {
            return null;
        }

        if (! isset($node->args[2]) || ! $node->args[2] instanceof Arg) {
            return null;
        }

        if (! $this->isTodayExpression($node->args[2]->value)) {
            return null;
        }

        return $this->renameAndKeepFirstArgument($node, self::WHERE_TODAY_METHODS[$callName][$operator]);
    }

    private function resolveStringArgumentValue(?Arg $arg): ?string
    {
        if (! $arg instanceof Arg) {
            return null;
        }

        if (! $arg->value instanceof String_) {
            return null;
        }

        return $arg->value->value;
    }

    private function renameAndKeepFirstArgument(MethodCall|StaticCall $node, string $methodName): Node
    {
        if (! isset($node->args[0])) {
            return $node;
        }

        $node->name = new Identifier($methodName);
        $node->args = [$node->args[0]];

        return $node;
    }

    private function isNowExpression(Expr $expr): bool
    {
        if ($expr instanceof FuncCall) {
            return $this->isName($expr, 'now') && $expr->args === [];
        }

        return $this->isCarbonStaticCallNamed($expr, 'now');
    }

    private function isTodayExpression(Expr $expr): bool
    {
        if ($expr instanceof FuncCall) {
            return $this->isName($expr, 'today') && $expr->args === [];
        }

        return $this->isCarbonStaticCallNamed($expr, 'today');
    }

    private function isCarbonStaticCallNamed(Expr $expr, string $methodName): bool
    {
        if (! $expr instanceof StaticCall) {
            return false;
        }

        if (! $this->isName($expr->name, $methodName) || $expr->args !== []) {
            return false;
        }

        return $this->isObjectType($expr->class, new ObjectType('Carbon\Carbon'))
            || $this->isObjectType($expr->class, new ObjectType('Illuminate\Support\Carbon'));
    }
}
