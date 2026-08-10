<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Support\Str;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
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
            $this->nodeNameResolver->isNames($methodCall->name, ['morphToMany', 'morphedByMany']) => 2,
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

    public function resolvePivotClass(ClassMethod $classMethod, MethodCall $relationCall, string $table): ?string
    {
        $usedPivotClass = $this->resolvePivotClassFromUsing($classMethod, $relationCall);

        // a pivot model the relation already declares is never second guessed by the models found by name
        if ($usedPivotClass !== null) {
            return $this->isPivotForTable($usedPivotClass, $table) ? $usedPivotClass : null;
        }

        foreach ($this->resolvePivotClassCandidates($relationCall, $table) as $pivotClass) {
            if ($this->isPivotForTable($pivotClass, $table)) {
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
     * The pivot model declared in the relation generics is preferred over the models found by name.
     *
     * @return iterable<string>
     */
    private function resolvePivotClassCandidates(MethodCall $relationCall, string $table): iterable
    {
        $declaredPivotClass = $this->resolvePivotClassFromGenerics($relationCall);

        if ($declaredPivotClass !== null) {
            yield $declaredPivotClass;
        }

        $classNames = array_unique([Str::studly($table), Str::studly(Str::singular($table))]);

        foreach ($this->resolveModelNamespaces($relationCall) as $modelNamespace) {
            foreach ($classNames as $className) {
                yield $modelNamespace . '\\' . $className;
            }
        }
    }

    /**
     * Read the pivot model from the third template type of the relation, e.g. BelongsToMany<Tag, $this, PostTag>.
     */
    private function resolvePivotClassFromGenerics(MethodCall $relationCall): ?string
    {
        $function = ScopeFetcher::fetch($relationCall)->getFunction();

        if ($function === null) {
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
    private function resolveModelNamespaces(MethodCall $relationCall): array
    {
        $classReflection = ScopeFetcher::fetch($relationCall)->getClassReflection();

        $classNames = [
            $classReflection instanceof ClassReflection ? $classReflection->getName() : null,
            $this->resolveClassName($relationCall->getArgs()[0] ?? null),
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

    private function isPivotForTable(string $pivotClass, string $table): bool
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
