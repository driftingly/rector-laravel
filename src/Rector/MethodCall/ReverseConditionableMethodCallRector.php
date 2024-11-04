<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\ReverseConditionableMethodCallRectorTest
 */
class ReverseConditionableMethodCallRector extends AbstractRector
{
    private const CONDITIONABLE_TRAIT = 'Illuminate\Support\Traits\Conditionable';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Reverse conditionable method calls',
            [
                new CodeSample(<<<'CODE_SAMPLE'
$conditionable->when(!$condition, function () {});
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
$conditionable->unless($condition, function () {});
CODE_SAMPLE
                ),
                new CodeSample(<<<'CODE_SAMPLE'
$conditionable->unless(!$condition, function () {});
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
$conditionable->when($condition, function () {});
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        if (! $this->isObjectType($node->var, new ObjectType(self::CONDITIONABLE_TRAIT))) {
            return null;
        }

        if (! $this->isNames($node->name, ['when', 'unless'])) {
            return null;
        }

        if ($node->isFirstClassCallable()) {
            return null;
        }

        if ($node->getArgs() === []) {
            return null;
        }

        $arg = $node->getArgs()[0];

        if (! $node->name instanceof Identifier) {
            return null;
        }

        if ($arg->value instanceof BooleanNot) {
            $node->args[0] = new Arg($arg->value->expr);
            $name = $node->name->toString() === 'when' ? 'unless' : 'when';

            $node->name = new Identifier($name);

            return $node;
        }

        return null;
    }
}
