<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\Node\Value\ValueResolver;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector\EloquentOrderByToLatestOrOldestRectorTest
 */
class EloquentOrderByToLatestOrOldestRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     */
    private QueryBuilderAnalyzer $queryBuilderAnalyzer;
    /**
     * @readonly
     */
    private ValueResolver $valueResolver;
    /**
     * @var string
     */
    public const ALLOWED_PATTERNS = 'allowed_patterns';

    /**
     * @var string[]
     */
    private array $allowedPatterns = ['*_at', '*_date', '*_on'];

    public function __construct(QueryBuilderAnalyzer $queryBuilderAnalyzer, ValueResolver $valueResolver)
    {
        $this->queryBuilderAnalyzer = $queryBuilderAnalyzer;
        $this->valueResolver = $valueResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes orderBy() to latest() or oldest()',
            [
                new ConfiguredCodeSample(<<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Builder;

$column = 'tested_at';

$builder->orderBy('created_at');
$builder->orderBy('created_at', 'desc');
$builder->orderBy('submitted_at');
$builder->orderByDesc('submitted_at');
$builder->orderBy($allowed_variable_name);
$builder->orderBy($unallowed_variable_name);
$builder->orderBy('unallowed_column_name');
CODE_SAMPLE
                    , <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Builder;

$column = 'tested_at';

$builder->oldest();
$builder->latest();
$builder->oldest('submitted_at');
$builder->latest('submitted_at');
$builder->oldest($allowed_variable_name);
$builder->orderBy($unallowed_variable_name);
$builder->orderBy('unallowed_column_name');
CODE_SAMPLE
                    , [self::ALLOWED_PATTERNS => [
                        'submitted_a*',
                        '*tested_at',
                        '$allowed_variable_name', ]]),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param  MethodCall  $node */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isOrderByMethodCall($node)) {
            return null;
        }

        $columnArg = $node->getArg('column', 0);
        $directionArg = $node->getArg('direction', 1);

        if ($columnArg === null) {
            return null;
        }

        if (! $this->isAllowedPattern($columnArg->value)) {
            return null;
        }

        $direction = $directionArg === null ? 'asc' : $this->valueResolver->getValue($directionArg);

        if (! is_string($direction)) {
            return null;
        }

        return $this->convertOrderByToLatest($node, $columnArg, $direction);
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $allowedPatterns = $configuration[self::ALLOWED_PATTERNS] ?? [];
        Assert::isArray($allowedPatterns);
        Assert::allString($allowedPatterns);

        $this->allowedPatterns = $allowedPatterns;
    }

    private function isOrderByMethodCall(MethodCall $methodCall): bool
    {
        return $this->queryBuilderAnalyzer->isMatchingCall($methodCall, 'orderBy')
            || $this->queryBuilderAnalyzer->isMatchingCall($methodCall, 'orderByDesc');
    }

    private function isAllowedPattern(Expr $expr): bool
    {
        // If no patterns are specified, consider all column names as matching
        if ($this->allowedPatterns === []) {
            return true;
        }

        $value = $this->valueResolver->getValue($expr);

        if (is_string($value)) {
            foreach ($this->allowedPatterns as $allowedPattern) {
                if (fnmatch($allowedPattern, $value)) {
                    return true;
                }
            }
        }

        if ($expr instanceof Variable && is_string($expr->name)) {
            foreach ($this->allowedPatterns as $allowedPattern) {
                if (fnmatch(ltrim($allowedPattern, '$'), $expr->name)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function convertOrderByToLatest(MethodCall $methodCall, Arg $columnArg, string $direction): MethodCall
    {
        if ($this->isName($methodCall->name, 'orderByDesc')) {
            $method = 'latest';
        } else {
            $method = strtolower($direction) === 'asc' ? 'oldest' : 'latest';
        }

        $methodCall->name = new Identifier($method);
        $methodCall->args = $this->valueResolver->isValue($columnArg->value, 'created_at')
            ? []
            : [$columnArg];

        return $methodCall;
    }
}
