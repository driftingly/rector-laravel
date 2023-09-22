<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector\EloquentOrderByToLatestOrOldestRectorTest
 */
class EloquentOrderByToLatestOrOldestRector extends AbstractRector
{
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

$builder->latest();
$builder->oldest();
$builder->latest('deleted_at');
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

        if ($this->isOrderByMethodCall($node)) {
            return $this->convertOrderByToLatest($node);
        }

        return null;
    }

    private function isOrderByMethodCall(MethodCall $methodCall): bool
    {
        // Check if it's a method call to `orderBy`

        return $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Database\Query\Builder'))
            && $methodCall->name instanceof Identifier
            && ($methodCall->name->name === 'orderBy' || $methodCall->name->name === 'orderByDesc')
            && $methodCall->args !== [];
    }

    private function convertOrderByToLatest(MethodCall $methodCall): MethodCall
    {
        if (! isset($methodCall->args[0]) && ! $methodCall->args[0] instanceof VariadicPlaceholder) {
            return $methodCall;
        }

        $columnVar = $methodCall->args[0]->value ?? null;
        if (! $columnVar instanceof Expr) {
            return $methodCall;
        }

        $direction = $methodCall->args[1]->value->value ?? 'asc';
        if ($this->isName($methodCall->name, 'orderByDesc')) {
            $newMethod = 'oldest';
        } else {
            $newMethod = $direction === 'asc' ? 'latest' : 'oldest';
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
