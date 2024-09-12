<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector\EloquentOrderByToLatestOrOldestRectorTest
 */
class EloquentOrderByToLatestOrOldestRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const ALLOWED_PATTERNS = 'allowed_patterns';

    /**
     * @var string[]
     */
    private $allowedPatterns = [];

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

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        if ($this->isOrderByMethodCall($node) && $this->isAllowedPattern($node)) {
            return $this->convertOrderByToLatest($node);
        }

        return null;
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
        // Check if it's a method call to `orderBy`

        return $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Database\Query\Builder'))
            && $methodCall->name instanceof Identifier
            && ($methodCall->name->name === 'orderBy' || $methodCall->name->name === 'orderByDesc')
            && count($methodCall->args) > 0;
    }

    private function isAllowedPattern(MethodCall $methodCall): bool
    {
        $columnArg = $methodCall->args[0]->value ?? null;

        // If no patterns are specified, consider all column names as matching
        if ($this->allowedPatterns === []) {
            return true;
        }

        if ($columnArg instanceof String_) {
            $columnName = $columnArg->value;

            // If specified, only allow certain patterns
            foreach ($this->allowedPatterns as $allowedPattern) {
                if (fnmatch($allowedPattern, $columnName)) {
                    return true;
                }
            }
        }

        if ($columnArg instanceof Variable && is_string($columnArg->name)) {
            // Check against allowed patterns
            foreach ($this->allowedPatterns as $allowedPattern) {
                if (fnmatch(ltrim($allowedPattern, '$'), $columnArg->name)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function convertOrderByToLatest(MethodCall $methodCall): MethodCall
    {
        if (! isset($methodCall->args[0]) && ! $methodCall->args[0] instanceof VariadicPlaceholder) {
            return $methodCall;
        }

        $columnVar = $methodCall->args[0]->value ?? null;
        if ($columnVar === null) {
            return $methodCall;
        }

        $direction = $methodCall->args[1]->value->value ?? 'asc';
        if ($this->isName($methodCall->name, 'orderByDesc')) {
            $newMethod = 'latest';
        } else {
            $newMethod = $direction === 'asc' ? 'oldest' : 'latest';
        }
        if ($columnVar instanceof String_ && $columnVar->value === 'created_at') {
            $methodCall->name = new Identifier($newMethod);
            $methodCall->args = [];

            return $methodCall;
        }

        if ($columnVar instanceof String_) {
            $methodCall->name = new Identifier($newMethod);
            $methodCall->args = [new Arg(new String_($columnVar->value))];

            return $methodCall;
        }

        $methodCall->name = new Identifier($newMethod);
        $methodCall->args = [new Arg($columnVar)];

        return $methodCall;
    }
}
