<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Rector\PhpParser\Node\Value\ValueResolver;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\FactoryHasForToMagicMethodRectorTest;
use ReflectionMethod;
use ReflectionNamedType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see FactoryHasForToMagicMethodRectorTest
 */
final class FactoryHasForToMagicMethodRector extends AbstractRector
{
    public function __construct(private readonly ValueResolver $valueResolver) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Encapsulate simple factory ->has()/->for() relationship calls into their magic method equivalents.',
            [
                new CodeSample(<<<'CODE_SAMPLE'
Product::factory()->has(Variation::factory()->times(3))->create();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
Product::factory()->hasVariations(3)->create();
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
        if (! $this->isRelationshipCall($node)) {
            return null;
        }

        $factory = $node->getArgs()[0]->value;
        $relatedClass = $this->resolveFactoryClass($factory);
        $arguments = $this->magicMethodArguments($node, $factory);

        if ($relatedClass === null || $arguments === null) {
            return null;
        }

        $relation = $this->resolveRelation($node, $relatedClass);

        if ($relation === null) {
            return null;
        }

        $node->name = new Identifier($this->getName($node->name) . ucfirst($relation));
        $node->args = array_map(static fn (Expr $value): Arg => new Arg($value), $arguments);

        return $node;
    }

    private function isRelationshipCall(MethodCall $node): bool
    {
        return $this->isNames($node->name, ['has', 'for'])
            && in_array(count($node->args), [1, 2], true)
            && $node->args[0] instanceof Arg;
    }

    private function resolveFactoryClass(Expr $expr): ?string
    {
        while ($expr instanceof MethodCall) {
            $expr = $expr->var;
        }

        if (! $expr instanceof StaticCall || ! $this->isName($expr->name, 'factory')) {
            return null;
        }

        return $this->getName($expr->class);
    }

    /**
     * @return list<Expr>|null
     */
    private function magicMethodArguments(MethodCall $node, Expr $factory): ?array
    {
        $count = null;
        $state = null;

        while ($factory instanceof MethodCall) {
            $name = $this->getName($factory->name);
            $value = $factory->args[0]->value ?? null;

            if (count($factory->args) !== 1 || ! $value instanceof Expr || ! in_array($name, ['times', 'count', 'state'], true)) {
                return null;
            }

            if ($name === 'state' && $state === null && $value instanceof Array_) {
                $state = $value;
            } elseif ($name !== 'state' && $count === null) {
                $count = $value;
            } else {
                return null;
            }

            $factory = $factory->var;
        }

        if ($factory instanceof StaticCall) {
            $merged = $this->mergeFactoryArguments($factory, $count, $state);

            if ($merged === null) {
                return null;
            }

            [$count, $state] = $merged;
        }

        if ($this->isName($node->name, 'for')) {
            return $state instanceof Array_ ? [$state] : [];
        }

        $arguments = [];

        if ($count instanceof Expr) {
            $arguments[] = $count;
        }

        if ($state instanceof Array_) {
            $arguments[] = $state;
        }

        return $arguments;
    }

    /**
     * @return array{Expr|null, Array_|null}|null
     */
    private function mergeFactoryArguments(StaticCall $factory, ?Expr $count, ?Array_ $state): ?array
    {
        if ($factory->args === []) {
            return [$count, $state];
        }

        if (count($factory->args) > 2 || ! $factory->args[0] instanceof Arg) {
            return null;
        }

        $first = $factory->args[0]->value;

        if ($first instanceof Array_) {
            return count($factory->args) === 1 ? [$count, $this->mergeState($first, $state)] : null;
        }

        if ($count !== null || ! is_numeric($this->valueResolver->getValue($first))) {
            return null;
        }

        $second = $factory->args[1] ?? null;

        if ($second === null) {
            return [$first, $state];
        }

        if (! $second instanceof Arg || ! $second->value instanceof Array_) {
            return null;
        }

        return [$first, $this->mergeState($second->value, $state)];
    }

    private function mergeState(Array_ $factoryState, ?Array_ $chainState): Array_
    {
        return $chainState === null
            ? $factoryState
            : new Array_(array_merge($factoryState->items, $chainState->items));
    }

    private function resolveRelation(MethodCall $node, string $relatedClass): ?string
    {
        $model = $this->resolveFactoryClass($node->var);

        if ($model === null || ! is_subclass_of($model, Model::class)) {
            return null;
        }

        $relation = $this->guessRelationName($node, $model, $relatedClass);

        if ($relation === null || ! $this->isEncapsulatableRelation($model, $relation)) {
            return null;
        }

        return $relation;
    }

    private function guessRelationName(MethodCall $node, string $model, string $relatedClass): ?string
    {
        if (isset($node->args[1]) && $node->args[1] instanceof Arg && $node->args[1]->value instanceof String_) {
            return $node->args[1]->value->value;
        }

        $basename = basename(str_replace('\\', '/', $relatedClass));
        $singular = lcfirst($basename);
        $plural = lcfirst($this->pluralise($basename));

        if ($this->isName($node->name, 'has') && method_exists($model, $plural)) {
            return $plural;
        }

        return method_exists($model, $singular) ? $singular : null;
    }

    /**
     * Best-effort English pluralisation of a class basename. The result is only ever used as a
     * candidate relation name that is then verified with method_exists(), so an inexact guess
     * simply causes the rule to fall back to the singular form or skip the node.
     */
    private function pluralise(string $word): string
    {
        $lower = strtolower($word);

        if (str_ends_with($lower, 'y') && preg_match('/[aeiou]y$/', $lower) === 0) {
            return substr($word, 0, -1) . 'ies';
        }

        if (preg_match('/(s|x|z|ch|sh)$/', $lower) === 1) {
            return $word . 'es';
        }

        return $word . 's';
    }

    private function isEncapsulatableRelation(string $model, string $relation): bool
    {
        if (! method_exists($model, $relation)) {
            return false;
        }

        $returnType = (new ReflectionMethod($model, $relation))->getReturnType();

        if (! $returnType instanceof ReflectionNamedType || $returnType->isBuiltin()) {
            return false;
        }

        $type = $returnType->getName();

        if (! is_a($type, Relation::class, true)) {
            return false;
        }

        return ! is_a($type, MorphTo::class, true); // MorphTo relations lack the necessary resolvable class from relation
    }
}
