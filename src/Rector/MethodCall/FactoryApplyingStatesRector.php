<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://laravel.com/docs/7.x/database-testing#creating-models
 * @see https://laravel.com/docs/8.x/database-testing#applying-states
 *
 * @see \Rector\Laravel\Tests\Rector\MethodCall\FactoryApplyingStatesRector\FactoryApplyingStatesRectorTest
 */
final class FactoryApplyingStatesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Call the state methods directly instead of specify the name of state.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$factory->state('delinquent');
$factory->states('premium', 'delinquent');
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
$factory->delinquent();
$factory->premium()->delinquent();
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Expr>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isNames($node->name, ['state', 'states'])) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Eloquent\FactoryBuilder'))) {
            return null;
        }

        $var = $node->var;
        $states = $this->getStatesFromArgs($node->args);
        foreach ($states as $state) {
            $var = $this->nodeFactory->createMethodCall($var, $state);
        }

        return $var;
    }

    /**
     * @param Arg[] $args
     * @return mixed[]
     */
    private function getStatesFromArgs(array $args): array
    {
        if (count($args) === 1) {
            return (array) $this->valueResolver->getValue($args[0]->value);
        }

        return array_map(fn ($arg) => $this->valueResolver->getValue($arg->value), $args);
    }
}
