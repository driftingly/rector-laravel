<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Support\Str;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\Php\PhpFunctionFromParserNodeReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PHPStan\ScopeFetcher;

final readonly class PivotClassAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private BetterNodeFinder $betterNodeFinder,
        private ModelAnalyzer $modelAnalyzer,
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function matchRelationTableArg(MethodCall $methodCall): ?Arg
    {
        $tablePosition = match (true) {
            $this->nodeNameResolver->isName($methodCall->name, 'belongsToMany') => 1,
            $this->isMorphRelation($methodCall) => 2,
            default => null,
        };

        if ($tablePosition === null) {
            return null;
        }

        if (! $this->nodeTypeResolver->isObjectType($methodCall->var, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        return $methodCall->getArg('table', $tablePosition);
    }

    public function resolvePivotClass(ClassMethod $classMethod, MethodCall $methodCall, string $table): ?string
    {
        $usedPivotClass = $this->resolvePivotClassFromUsing($classMethod, $methodCall);

        // a pivot the relation declares itself is never second guessed by the models found by name
        if ($usedPivotClass !== null) {
            return $this->isModelForTable($usedPivotClass, $table) ? $usedPivotClass : null;
        }

        // a relation declaring no pivot gets the default one from its generics, which never matches the table
        $declaredPivotClass = $this->resolvePivotClassFromGenerics($methodCall);

        if ($declaredPivotClass !== null && $this->isModelForTable($declaredPivotClass, $table)) {
            return $declaredPivotClass;
        }

        foreach ($this->resolvePivotClassCandidates($methodCall, $table) as $pivotClass) {
            if (! $this->isModelForTable($pivotClass, $table)) {
                continue;
            }

            if ($this->isPivotModel($pivotClass) || $this->tableFollowsRelationConvention($methodCall, $table)) {
                return $pivotClass;
            }
        }

        return null;
    }

    private function resolvePivotClassFromUsing(ClassMethod $classMethod, MethodCall $relationCall): ?string
    {
        foreach ($this->betterNodeFinder->findInstancesOfInFunctionLikeScoped($classMethod, MethodCall::class) as $methodCall) {
            if (! $this->nodeNameResolver->isName($methodCall->name, 'using')) {
                continue;
            }

            if (! $this->isChainedOnto($methodCall, $relationCall)) {
                continue;
            }

            return $this->resolveClassName($methodCall->getArgs()[0] ?? null);
        }

        return null;
    }

    private function isChainedOnto(MethodCall $methodCall, MethodCall $relationCall): bool
    {
        $call = $methodCall->var;

        while ($call instanceof MethodCall) {
            if ($call === $relationCall) {
                return true;
            }

            $call = $call->var;
        }

        return false;
    }

    /**
     * Pivot models are named after the table they hold, so post_tag gives PostTag.
     *
     * @return iterable<string>
     */
    private function resolvePivotClassCandidates(MethodCall $methodCall, string $table): iterable
    {
        $joinedModels = [$this->resolveDeclaringModel($methodCall), $this->resolveRelatedModel($methodCall)];

        $classNames = array_unique([Str::studly($table), Str::studly(Str::singular($table))]);

        foreach ($this->resolveModelNamespaces($methodCall) as $modelNamespace) {
            foreach ($classNames as $className) {
                $pivotClass = $modelNamespace . '\\' . $className;

                // a pivot is a model of its own, never one of the two models the relation joins
                if (in_array($pivotClass, $joinedModels, true)) {
                    continue;
                }

                yield $pivotClass;
            }
        }
    }

    /**
     * Read the pivot model from the third template type of the relation, e.g. BelongsToMany<Tag, $this, PostTag>.
     */
    private function resolvePivotClassFromGenerics(MethodCall $methodCall): ?string
    {
        $function = ScopeFetcher::fetch($methodCall)->getFunction();

        if (! $function instanceof PhpFunctionFromParserNodeReflection) {
            return null;
        }

        $pivotType = $function->getPhpDocReturnType()
            ->getTemplateType('Illuminate\Database\Eloquent\Relations\BelongsToMany', 'TPivotModel');

        $classNames = $pivotType->getObjectClassNames();

        if (count($classNames) !== 1) {
            return null;
        }

        return $classNames[0];
    }

    /**
     * Pivot models live alongside the models relating through them, so no namespace has to be configured.
     *
     * @return list<string>
     */
    private function resolveModelNamespaces(MethodCall $methodCall): array
    {
        $classNames = [
            $this->resolveDeclaringModel($methodCall),
            $this->resolveRelatedModel($methodCall),
        ];

        $modelNamespaces = [];

        foreach ($classNames as $className) {
            if ($className === null) {
                continue;
            }

            $modelNamespace = Str::beforeLast($className, '\\');

            if ($modelNamespace !== '' && $modelNamespace !== $className) {
                $modelNamespaces[] = $modelNamespace;
            }
        }

        return array_values(array_unique($modelNamespaces));
    }

    private function resolveDeclaringModel(MethodCall $methodCall): ?string
    {
        $classNames = $this->nodeTypeResolver->getType($methodCall->var)->getObjectClassNames();

        if (count($classNames) !== 1) {
            return null;
        }

        return $classNames[0];
    }

    private function resolveRelatedModel(MethodCall $methodCall): ?string
    {
        return $this->resolveClassName($methodCall->getArgs()[0] ?? null);
    }

    private function resolveClassName(?Arg $arg): ?string
    {
        if (! $arg instanceof Arg) {
            return null;
        }

        $classNames = $this->nodeTypeResolver->getType($arg->value)
            ->getClassStringObjectType()
            ->getObjectClassNames();

        if (count($classNames) !== 1) {
            return null;
        }

        return $classNames[0];
    }

    /**
     * The framework treats a model using the trait as a pivot, which covers the Pivot and MorphPivot classes as well.
     */
    private function isPivotModel(string $pivotClass): bool
    {
        if (! $this->reflectionProvider->hasClass($pivotClass)) {
            return false;
        }

        return $this->reflectionProvider->getClass($pivotClass)
            ->hasTraitUse('Illuminate\Database\Eloquent\Relations\Concerns\AsPivot');
    }

    /**
     * A relation without a table of its own is given one built from the models it joins, so a model matched by the
     * table name is only taken for a pivot when the table is the one the framework would have built.
     */
    private function tableFollowsRelationConvention(MethodCall $methodCall, string $table): bool
    {
        if ($this->isMorphRelation($methodCall)) {
            $morphName = $methodCall->getArg('name', 1)?->value;

            // the framework pluralises the last word of the relationship name, which is what Str::plural does
            return $morphName instanceof String_ && Str::plural($morphName->value) === $table;
        }

        $declaringModel = $this->resolveDeclaringModel($methodCall);
        $relatedModel = $this->resolveRelatedModel($methodCall);

        if ($declaringModel === null || $relatedModel === null) {
            return false;
        }

        return $this->modelAnalyzer->getJoiningTable(
            new ObjectType($declaringModel),
            new ObjectType($relatedModel)
        ) === $table;
    }

    private function isMorphRelation(MethodCall $methodCall): bool
    {
        return $this->nodeNameResolver->isNames($methodCall->name, ['morphToMany', 'morphedByMany']);
    }

    private function isModelForTable(string $pivotClass, string $table): bool
    {
        if (! $this->reflectionProvider->hasClass($pivotClass)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($pivotClass);

        if (! $classReflection->isClass() || $classReflection->isAbstract()) {
            return false;
        }

        if (! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass('Illuminate\Database\Eloquent\Model'))) {
            return false;
        }

        return $this->modelAnalyzer->getTable(new ObjectType($pivotClass)) === $table;
    }
}
