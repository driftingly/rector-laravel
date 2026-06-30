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
use Rector\Contract\DependencyInjection\ResettableInterface;
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

    private ?string $initializedProjectRoot = null;

    public function __construct(
        private readonly RectorParser $rectorParser,
        private readonly NodeNameResolver $nodeNameResolver,
    ) {}

    public function reset(): void
    {
        $this->observerClassesByModel = [];
        $this->canUpdateModelCache = [];
        $this->classFilePaths = [];
        $this->initializedProjectRoot = null;
    }

    public function matchObserveStaticCall(StaticCall $staticCall): ?ObservedByRegistration
    {
        if (! $this->nodeNameResolver->isName($staticCall->name, 'observe')) {
            return null;
        }

        $modelClass = $this->nodeNameResolver->getName($staticCall->class);
        if (! is_string($modelClass)) {
            return null;
        }

        $observerClasses = $this->resolveObserverClassesFromArgs($staticCall->args);
        if ($observerClasses === null) {
            return null;
        }

        return new ObservedByRegistration($modelClass, $observerClasses);
    }

    /**
     * @return list<class-string>
     */
    public function resolveObserverClassesForModel(string $modelClass, string $currentFilePath): array
    {
        $this->initializeFromProjectRoot($this->resolveProjectRoot($currentFilePath));

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
        $this->initializeFromProjectRoot($this->resolveProjectRoot($currentFilePath));

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

    private function initializeFromProjectRoot(string $projectRoot): void
    {
        if ($this->initializedProjectRoot === $projectRoot) {
            return;
        }

        $this->observerClassesByModel = [];
        $this->canUpdateModelCache = [];
        $this->classFilePaths = [];
        $this->initializedProjectRoot = $projectRoot;

        foreach ($this->findPhpFiles($projectRoot) as $filePath) {
            $this->collectObserveRegistrationsFromFile($filePath);
        }
    }

    private function collectObserveRegistrationsFromFile(string $filePath): void
    {
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
            if ($node instanceof Class_) {
                $className = $this->resolveClassName($node);
                if (is_string($className) && ! array_key_exists($className, $this->classFilePaths)) {
                    $this->classFilePaths[$className] = $filePath;
                }

                return null;
            }

            if (! $node instanceof ClassMethod) {
                return null;
            }

            if (! $this->nodeNameResolver->isName($node->name, 'boot')) {
                return null;
            }

            foreach ((array) $node->stmts as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof StaticCall) {
                    continue;
                }

                /** @var StaticCall $staticCall */
                $staticCall = $stmt->expr;
                $observedByRegistration = $this->matchObserveStaticCall($staticCall);
                if (! $observedByRegistration instanceof ObservedByRegistration) {
                    continue;
                }

                $existingObserverClasses = $this->observerClassesByModel[$observedByRegistration->modelClass] ?? [];
                $this->observerClassesByModel[$observedByRegistration->modelClass] = $this->uniqueObserverClasses([
                    ...$existingObserverClasses,
                    ...$observedByRegistration->observerClasses,
                ]);
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
     * @return iterable<string>
     */
    private function findPhpFiles(string $projectRoot): iterable
    {
        $directories = [$projectRoot];

        while ($directories !== []) {
            $directory = array_pop($directories);
            if (! is_string($directory)) {
                continue;
            }

            $items = scandir($directory);
            if ($items === false) {
                continue;
            }

            sort($items);

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $path = $directory . '/' . $item;

                if (is_dir($path)) {
                    if (in_array($item, ['.git', '.idea', '.vscode', 'build', 'vendor'], true)) {
                        continue;
                    }

                    $directories[] = $path;
                    continue;
                }

                if (! str_ends_with($path, '.php')) {
                    continue;
                }

                yield $path;
            }
        }
    }

    /**
     * @param  list<class-string>  $observerClasses
     * @return list<class-string>
     */
    private function uniqueObserverClasses(array $observerClasses): array
    {
        return array_values(array_unique($observerClasses));
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
