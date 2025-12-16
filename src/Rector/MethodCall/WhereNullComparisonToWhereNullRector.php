<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class WhereNullComparisonToWhereNullRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert to where comparison to whereNull method call', [
            new CodeSample(<<<'CODE_SAMPLE'
$query->where('foo', null);
$query->where('foo', '=', null);
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
$query->whereNull('foo');
$query->whereNull('foo');
CODE_SAMPLE
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    /**
     * @param Node\Expr\MethodCall $node
     * @return Node\Expr\MethodCall|null
     */
    public function refactor(Node $node): ?MethodCall
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        if (! $this->isName($node->name, 'where')) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Database\Query\Builder'))) {
            return null;
        }

        $args = $node->args;
        $type = null;

        if (count($args) === 2 && $args[1] instanceof Node\Arg) {
            $type = $this->getType($args[1]->value);
        }

        if (count($args) === 3 && $args[2] instanceof Node\Arg) {
            $type = $this->getType($args[2]->value);
        }

        if ($type === null) {
            return null;
        }

        if ($type->isNull()->no()) {
            return null;
        }

        return $this->nodeFactory->createMethodCall(
            $node->var,
            'whereNull',
            [$args[0]]
        );
    }
}
