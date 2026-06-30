<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\Contract\DependencyInjection\ResettableInterface;
use Rector\FileSystem\FilesFinder;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use Rector\PhpParser\Parser\RectorParser;
use RectorLaravel\ValueObject\ObservedByRegistration;
use Throwable;

final class ObservedByAnalyzer implements ResettableInterface
{
    private const string OBSERVED_BY_ATTRIBUTE = 'Illuminate\\Database\\Eloquent\\Attributes\\ObservedBy';

    /**
     * @var array<string, list<class-string>>
     */
    private array $observerClassesByModel = [];

    /**
     * @var array<string, bool>
     */
    private array $canUpdateModelCache = [];

    /**
     * @var array<class-string, string>
     */
    private array $classFilePaths = [];

    /**
     * @var array<string, true>
     */
    private array $indexedFilePaths = [];

    private ?string $initializedProjectRoot = null;

    public function __construct(
        private readonly RectorParser $rectorParser,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly FilesFinder $filesFinder,
    ) {}

    public function reset(): void
    {
        $this->observerClassesByModel = [];
        $this->canUpdateModelCache = [];
        $this->classFilePaths = [];
        $this->indexedFilePaths = [];
        $this->initializedProjectRoot = null;
    }

    public function matchObserveStaticCall(StaticCall $staticCall, ?string $currentClassName = null): ?ObservedByRegistration
    {
        if (! $this->nodeNameResolver->isName($staticCall->name, 'observe')) {
            return null;
        }

        $modelClass = $this->resolveObservedModelClass($staticCall, $currentClassName);
        if (! is_string($modelClass)) {
            return null;
        }

        $observerClasses = $this->resolveObserverClassesFromArgs($staticCall->args);
        if ($observerClasses === null) {
            return null;
        }

        return new ObservedByRegistration($modelClass, $observerClasses);
    }

    public function resolveCurrentClassName(Node $node): ?string
    {
        $parentNode = $node->getAttribute('parent');
        while ($parentNode instanceof Node) {
            if ($parentNode instanceof Class_) {
                return $this->resolveClassName($parentNode);
            }

            $parentNode = $parentNode->getAttribute('parent');
        }

        $scope = $node->getAttribute('scope');
        if (! is_object($scope) || ! method_exists($scope, 'getClassReflection')) {
            return null;
        }

        /** @var object{getClassReflection: callable(): mixed} $scope */
        $classReflection = $scope->getClassReflection();
        if (! is_object($classReflection) || ! method_exists($classReflection, 'getName')) {
            return null;
        }

        $className = $classReflection->getName();

        return is_string($className) ? $className : null;
    }

    /**
     * @return list<class-string>
     */
    public function resolveObserverClassesForModel(string $modelClass, string $currentFilePath): array
    {
        $this->initializeFromProjectRoot($this->resolveProjectRoot($currentFilePath), $currentFilePath);

        return $this->observerClassesByModel[$modelClass] ?? [];
    }

    /**
     * @return list<class-string>|null
     */
    public function resolveExistingObservedByClasses(Class_ $class): ?array
    {
        $observerClasses = [];
        $hasObservedByAttribute = false;

        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if (! $this->nodeNameResolver->isName($attr->name, self::OBSERVED_BY_ATTRIBUTE)) {
                    continue;
                }

                $hasObservedByAttribute = true;
                $resolvedObserverClasses = $this->resolveObserverClassesFromAttribute($attr);
                if ($resolvedObserverClasses === null) {
                    return null;
                }

                $observerClasses = array_merge($observerClasses, $resolvedObserverClasses);
            }
        }

        if (! $hasObservedByAttribute) {
            return [];
        }

        return $this->uniqueObserverClasses($observerClasses);
    }

    public function isLikelyEloquentModelClass(Class_ $class): bool
    {
        if (! $class->extends instanceof Node\Name) {
            return false;
        }

        $extendedClass = $this->nodeNameResolver->getName($class->extends);
        if (! is_string($extendedClass)) {
            return false;
        }

        return in_array($extendedClass, [
            'Illuminate\\Database\\Eloquent\\Model',
            'Illuminate\\Foundation\\Auth\\User',
            'Model',
            'Authenticatable',
        ], true)
            || str_ends_with($extendedClass, '\\Model')
            || str_ends_with($extendedClass, '\\Authenticatable');
    }

    /**
     * @param  list<class-string>  $observerClasses
     */
    public function canUpdateModel(string $modelClass, array $observerClasses, string $currentFilePath): bool
    {
        $this->initializeFromProjectRoot($this->resolveProjectRoot($currentFilePath), $currentFilePath);

        $cacheKey = $modelClass . '|' . implode('|', $observerClasses);
        if (array_key_exists($cacheKey, $this->canUpdateModelCache)) {
            return $this->canUpdateModelCache[$cacheKey];
        }

        $fileName = $this->classFilePaths[$modelClass] ?? null;
        if (! is_string($fileName)) {
            return $this->canUpdateModelCache[$cacheKey] = false;
        }

        $class = $this->findClassInFile($fileName, $modelClass);
        if (! $class instanceof Class_) {
            return $this->canUpdateModelCache[$cacheKey] = false;
        }

        if (! $this->isLikelyEloquentModelClass($class)) {
            return $this->canUpdateModelCache[$cacheKey] = false;
        }

        $existingObservedByClasses = $this->resolveExistingObservedByClasses($class);
        if ($existingObservedByClasses === null) {
            return $this->canUpdateModelCache[$cacheKey] = false;
        }

        return $this->canUpdateModelCache[$cacheKey] = true;
    }

    /**
     * @param  Arg[]  $args
     * @return list<class-string>|null
     */
    private function resolveObserverClassesFromArgs(array $args): ?array
    {
        if (count($args) !== 1) {
            return null;
        }

        if ($args[0]->name !== null) {
            return null;
        }

        return $this->resolveObserverClassesFromExpr($args[0]->value);
    }

    /**
     * @return list<class-string>|null
     */
    private function resolveObserverClassesFromAttribute(Attribute $attribute): ?array
    {
        if (count($attribute->args) !== 1) {
            return null;
        }

        if ($attribute->args[0]->name !== null) {
            return null;
        }

        return $this->resolveObserverClassesFromExpr($attribute->args[0]->value);
    }

    /**
     * @return list<class-string>|null
     */
    private function resolveObserverClassesFromExpr(Expr $expr): ?array
    {
        if ($expr instanceof ClassConstFetch) {
            if (! $this->nodeNameResolver->isName($expr->name, 'class')) {
                return null;
            }

            $observerClass = $this->nodeNameResolver->getName($expr->class);
            if (! is_string($observerClass)) {
                return null;
            }

            return [$observerClass];
        }

        if (! $expr instanceof Array_) {
            return null;
        }

        $observerClasses = [];

        foreach ($expr->items as $item) {
            if ($item === null) {
                return null;
            }

            if ($item->key !== null) {
                return null;
            }

            $itemValue = $item->value;
            if (! $itemValue instanceof ClassConstFetch) {
                return null;
            }

            if (! $this->nodeNameResolver->isName($itemValue->name, 'class')) {
                return null;
            }

            $observerClass = $this->nodeNameResolver->getName($itemValue->class);
            if (! is_string($observerClass)) {
                return null;
            }

            $observerClasses[] = $observerClass;
        }

        if ($observerClasses === []) {
            return null;
        }

        return $this->uniqueObserverClasses($observerClasses);
    }

    private function initializeFromProjectRoot(string $projectRoot, string $currentFilePath): void
    {
        if ($this->initializedProjectRoot === $projectRoot) {
            if (! isset($this->indexedFilePaths[$currentFilePath]) && is_file($currentFilePath)) {
                $this->collectObserveRegistrationsFromFile($currentFilePath);
            }

            return;
        }

        $this->observerClassesByModel = [];
        $this->canUpdateModelCache = [];
        $this->classFilePaths = [];
        $this->indexedFilePaths = [];
        $this->initializedProjectRoot = $projectRoot;

        foreach ($this->resolveProjectFiles($projectRoot, $currentFilePath) as $filePath) {
            $this->collectObserveRegistrationsFromFile($filePath);
        }
    }

    private function collectObserveRegistrationsFromFile(string $filePath): void
    {
        $this->indexedFilePaths[$filePath] = true;

        try {
            $stmts = $this->rectorParser->parseFile($filePath);
        } catch (Throwable) {
            return;
        }

        if ($stmts === []) {
            return;
        }

        $resolvedStmts = $this->resolveNames($stmts);

        SimpleCallableNodeTraverser::traverseNodesWithCallable($resolvedStmts, function (Node $node) use ($filePath): null {
            if (! $node instanceof Class_) {
                return null;
            }

            $className = $this->resolveClassName($node);
            if (! is_string($className)) {
                return null;
            }

            if (! array_key_exists($className, $this->classFilePaths)) {
                $this->classFilePaths[$className] = $filePath;
            }

            foreach ($node->getMethods() as $classMethod) {
                if (! $this->isObserverRegistrationMethod($classMethod)) {
                    continue;
                }

                foreach ((array) $classMethod->stmts as $stmt) {
                    if (! $stmt instanceof Expression) {
                        continue;
                    }

                    if (! $stmt->expr instanceof StaticCall) {
                        continue;
                    }

                    /** @var StaticCall $staticCall */
                    $staticCall = $stmt->expr;
                    $observedByRegistration = $this->matchObserveStaticCall($staticCall, $className);
                    if (! $observedByRegistration instanceof ObservedByRegistration) {
                        continue;
                    }

                    $existingObserverClasses = $this->observerClassesByModel[$observedByRegistration->modelClass] ?? [];
                    $this->observerClassesByModel[$observedByRegistration->modelClass] = $this->uniqueObserverClasses([
                        ...$existingObserverClasses,
                        ...$observedByRegistration->observerClasses,
                    ]);
                }
            }

            return null;
        });
    }

    /**
     * @param  array<int, Namespace_|Class_|Node\Stmt>  $stmts
     * @return array<int, Namespace_|Class_|Node\Stmt>
     */
    private function resolveNames(array $stmts): array
    {
        $nameResolver = new NameResolver(null, [
            'replaceNodes' => false,
            'preserveOriginalNames' => true,
        ]);

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor($nameResolver);

        /** @var array<int, Namespace_|Class_|Node\Stmt> $resolvedStmts */
        $resolvedStmts = $nodeTraverser->traverse($stmts);

        return $resolvedStmts;
    }

    private function resolveProjectRoot(string $currentFilePath): string
    {
        $directory = is_dir($currentFilePath) ? $currentFilePath : dirname($currentFilePath);

        while ($directory !== dirname($directory)) {
            if (is_file($directory . '/composer.json')) {
                return $directory;
            }

            $directory = dirname($directory);
        }

        return dirname($currentFilePath);
    }

    private function findClassInFile(string $filePath, string $className): ?Class_
    {
        try {
            $stmts = $this->rectorParser->parseFile($filePath);
        } catch (Throwable) {
            return null;
        }

        if ($stmts === []) {
            return null;
        }

        $resolvedStmts = $this->resolveNames($stmts);
        $foundClass = null;

        SimpleCallableNodeTraverser::traverseNodesWithCallable($resolvedStmts, function (Node $node) use ($className, &$foundClass): null {
            if (! $node instanceof Class_) {
                return null;
            }

            if ($this->resolveClassName($node) !== $className) {
                return null;
            }

            $foundClass = $node;

            return null;
        });

        return $foundClass;
    }

    /**
     * @return list<string>
     */
    private function resolveProjectFiles(string $projectRoot, string $currentFilePath): array
    {
        $configuredPaths = array_filter(
            SimpleParameterProvider::provideArrayParameter(Option::PATHS),
            static fn (mixed $path): bool => is_string($path)
        );

        $projectPaths = [];

        foreach ($configuredPaths as $configuredPath) {
            if ($configuredPath === $projectRoot || str_starts_with($configuredPath, $projectRoot . '/')) {
                $projectPaths[] = $configuredPath;
            }
        }

        if ($projectPaths === []) {
            $projectPaths = [$projectRoot];
        }

        $filePaths = $this->filesFinder->findInDirectoriesAndFiles($projectPaths, ['php']);

        if (is_file($currentFilePath)) {
            $filePaths[] = $currentFilePath;
        }

        return array_values(array_unique($filePaths));
    }

    /**
     * @param  list<class-string>  $observerClasses
     * @return list<class-string>
     */
    private function uniqueObserverClasses(array $observerClasses): array
    {
        return array_values(array_unique($observerClasses));
    }

    private function isObserverRegistrationMethod(ClassMethod $classMethod): bool
    {
        return $this->nodeNameResolver->isNames($classMethod->name, ['boot', 'booted']);
    }

    private function resolveObservedModelClass(StaticCall $staticCall, ?string $currentClassName): ?string
    {
        if ($currentClassName !== null && $staticCall->class instanceof Name && $this->nodeNameResolver->isNames($staticCall->class, ['self', 'static'])) {
            return $currentClassName;
        }

        $modelClass = $this->nodeNameResolver->getName($staticCall->class);
        if (! is_string($modelClass)) {
            return null;
        }

        return $modelClass;
    }

    private function resolveClassName(Class_ $class): ?string
    {
        if (property_exists($class, 'namespacedName') && $class->namespacedName instanceof Name) {
            return $class->namespacedName->toString();
        }

        if ($class->name === null) {
            return null;
        }

        return $class->name->toString();
    }
}
