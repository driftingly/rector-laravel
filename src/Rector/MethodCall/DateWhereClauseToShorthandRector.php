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
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\DateWhereClauseToShorthandRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/54408
 *
 * @see DateWhereClauseToShorthandRectorTest
 */
final class DateWhereClauseToShorthandRector extends AbstractRector
{
    /**
     * @readonly
     */
    private QueryBuilderAnalyzer $queryBuilderAnalyzer;
    /**
     * @var mixed[]
     */
    private const WHERE_NOW_METHODS = [
        'where' => [
            '<' => 'wherePast',
            '<=' => 'whereNowOrPast',
            '>' => 'whereFuture',
            '>=' => 'whereNowOrFuture',
        ],
        'orWhere' => [
            '<' => 'orWherePast',
            '<=' => 'orWhereNowOrPast',
            '>' => 'orWhereFuture',
            '>=' => 'orWhereNowOrFuture',
        ],
    ];

    /**
     * @var mixed[]
     */
    private const WHERE_TODAY_METHODS = [
        'whereDate' => [
            '=' => 'whereToday',
            '<' => 'whereBeforeToday',
            '<=' => 'whereTodayOrBefore',
            '>' => 'whereAfterToday',
            '>=' => 'whereTodayOrAfter',
        ],
        'orWhereDate' => [
            '=' => 'orWhereToday',
            '<' => 'orWhereBeforeToday',
            '<=' => 'orWhereTodayOrBefore',
            '>' => 'orWhereAfterToday',
            '>=' => 'orWhereTodayOrAfter',
        ],
    ];

    public function __construct(QueryBuilderAnalyzer $queryBuilderAnalyzer)
    {
        $this->queryBuilderAnalyzer = $queryBuilderAnalyzer;
    }

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
        $callName = $this->getName($node->name);
        if (! is_string($callName)) {
            return null;
        }

        if (! $this->queryBuilderAnalyzer->isMatchingCall($node, $callName)) {
            return null;
        }

        if (isset(self::WHERE_NOW_METHODS[$callName])) {
            return $this->refactorWhereNowCall($node, $callName);
        }

        if (isset(self::WHERE_TODAY_METHODS[$callName])) {
            return $this->refactorWhereTodayCall($node, $callName);
        }

        return null;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function refactorWhereNowCall($node, string $callName): ?Node
    {
        if (count($node->args) !== 3) {
            return null;
        }

        if (! $node->args[1] instanceof Arg) {
            return null;
        }

        $operator = $this->resolveStringArgumentValue($node->args[1]);
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

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function refactorWhereTodayCall($node, string $callName): ?Node
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

        if (! $node->args[1] instanceof Arg) {
            return null;
        }

        $operator = $this->resolveStringArgumentValue($node->args[1]);
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

    private function resolveStringArgumentValue(Arg $arg): ?string
    {
        if (! $arg->value instanceof String_) {
            return null;
        }

        return $arg->value->value;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function renameAndKeepFirstArgument($node, string $methodName): Node
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

        return $this->isDateTimeStaticCallNamed($expr, 'now');
    }

    private function isTodayExpression(Expr $expr): bool
    {
        if ($expr instanceof FuncCall) {
            return $this->isName($expr, 'today') && $expr->args === [];
        }

        return $this->isDateTimeStaticCallNamed($expr, 'today');
    }

    private function isDateTimeStaticCallNamed(Expr $expr, string $methodName): bool
    {
        if (! $expr instanceof StaticCall) {
            return false;
        }

        if (! $this->isName($expr->name, $methodName) || $expr->args !== []) {
            return false;
        }

        $objectType = new ObjectType('DateTimeInterface');
        $callerType = $this->nodeTypeResolver->getType($expr->class);

        return $objectType->isSuperTypeOf($callerType)->yes();
    }
}
