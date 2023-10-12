<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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
    final public const ALLOWED_PATTERNS = 'allowed_patterns';

    /**
     * @var string[]
     */
    private array $allowedPatterns = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes orderBy() to latest() or oldest()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Builder;

$builder->orderBy('created_at');
$builder->orderBy('created_at', 'desc');
$builder->orderBy('deleted_at');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Builder;

$builder->oldest();
$builder->latest();
$builder->oldest('deleted_at');
CODE_SAMPLE
                    ,
                ),
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

    private function isOrderByMethodCall(MethodCall $methodCall): bool
    {
        // Check if it's a method call to `orderBy`

        return $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Database\Query\Builder'))
            && $methodCall->name instanceof Node\Identifier
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

        if ($columnArg instanceof Node\Scalar\String_) {
            $columnName = $columnArg->value;

            // If specified, only allow certain patterns
            foreach ($this->allowedPatterns as $pattern) {
                if (fnmatch($pattern, $columnName)) {
                    return true;
                }
            }
        }

        if ($columnArg instanceof Node\Expr\Variable && is_string($columnArg->name)) {
            // Check against allowed patterns
            foreach ($this->allowedPatterns as $pattern) {
                if (fnmatch(ltrim($pattern, '$'), $columnArg->name)) {
                    return true;
                }
            }
        }


        return false;
    }

    private function convertOrderByToLatest(MethodCall $methodCall): MethodCall
    {
        if (! isset($methodCall->args[0]) && ! $methodCall->args[0] instanceof Node\VariadicPlaceholder) {
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
        if ($columnVar instanceof Node\Scalar\String_ && $columnVar->value === 'created_at') {
            $methodCall->name = new Node\Identifier($newMethod);
            $methodCall->args = [];

            return $methodCall;
        }

        if ($columnVar instanceof Node\Scalar\String_) {
            $methodCall->name = new Node\Identifier($newMethod);
            $methodCall->args = [new Node\Arg(new Node\Scalar\String_($columnVar->value))];

            return $methodCall;
        }

        $methodCall->name = new Node\Identifier($newMethod);
        $methodCall->args = [new Node\Arg($columnVar)];

        return $methodCall;
    }


    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $allowedPatterns = $configuration[self::ALLOWED_PATTERNS] ?? [];
        Assert::isArray($allowedPatterns);

        $this->allowedPatterns = $allowedPatterns;
    }
}
