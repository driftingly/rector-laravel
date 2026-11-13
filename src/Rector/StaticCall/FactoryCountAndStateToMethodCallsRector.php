<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\FactoryCountAndStateToMethodCallsRectorTest;

/**
 * @changelog https://laravel.com/docs/12.x/eloquent-factories#creating-models-using-factories
 *
 * @see FactoryCountAndStateToMethodCallsRectorTest
 */
final class FactoryCountAndStateToMethodCallsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Move the count and state arguments of `HasFactory::factory()` into `count()` and `state()` calls.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
User::factory(3);
User::factory(['name' => 'Taylor']);
User::factory(3, ['name' => 'Taylor']);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
User::factory()->count(3);
User::factory()->state(['name' => 'Taylor']);
User::factory()->count(3)->state(['name' => 'Taylor']);
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
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'factory') || $node->isFirstClassCallable() || $node->args === []) {
            return null;
        }

        if (! $this->callsHasFactoryMethod($node)) {
            return null;
        }

        $methodCalls = $this->resolveMethodCalls($node);
        if ($methodCalls === null) {
            return null;
        }

        $result = new StaticCall($node->class, $node->name);
        foreach ($methodCalls as [$methodName, $expr]) {
            $result = $this->nodeFactory->createMethodCall($result, $methodName, [$expr]);
        }

        return $result;
    }

    /**
     * Late static binding or an expression receiver may dispatch to a model that overrides factory().
     */
    private function callsHasFactoryMethod(StaticCall $staticCall): bool
    {
        if (! $staticCall->class instanceof Name || $staticCall->class->isSpecialClassName()) {
            return false;
        }

        $classReflections = $this->getType($staticCall->class)->getObjectClassReflections();
        if (count($classReflections) !== 1) {
            return false;
        }

        $classReflection = $classReflections[0];
        if (! $classReflection->hasNativeMethod('factory')) {
            return false;
        }

        $factoryMethod = $classReflection->getNativeReflection()->getMethod('factory')->getBetterReflection();

        return $factoryMethod->getDeclaringClass()->getName() === HasFactory::class;
    }

    /**
     * Keeps the source order of the arguments, so their evaluation order stays the same.
     *
     * @return list<array{'count'|'state', Expr}>|null
     */
    private function resolveMethodCalls(StaticCall $staticCall): ?array
    {
        $methodCalls = [];

        foreach ($staticCall->args as $position => $arg) {
            if (! $arg instanceof Arg || $arg->unpack) {
                return null;
            }

            $type = $this->getType($arg->value);

            if ($position === 0 && ! $arg->name instanceof Identifier && ($type->isArray()->yes() || $type->isCallable()->yes())) {
                // Laravel uses an array or callable count as the state and ignores the second argument
                if (count($staticCall->args) > 1) {
                    return null;
                }

                return [['state', $arg->value]];
            }

            $name = $arg->name?->toString() ?? ['count', 'state'][$position] ?? null;

            if ($name === 'count' && $arg->value instanceof ConstFetch && $this->isName($arg->value, 'null')) {
                continue;
            }

            $isIntegerCount = $name === 'count' && $type->isInteger()->yes();
            if ($name !== 'state' && ! $isIntegerCount) {
                return null;
            }

            $methodCalls[] = [$name, $arg->value];
        }

        return $methodCalls;
    }
}
