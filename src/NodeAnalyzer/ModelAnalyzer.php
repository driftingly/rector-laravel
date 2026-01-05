<?php

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use ReflectionException;
use Throwable;

class ModelAnalyzer
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly NodeNameResolver $nodeNameResolver,
    ) {}

    protected static function relationType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Relations\Relation');
    }

    /**
     * Returns the table name of a model
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function getTable(string|ObjectType $model): ?string
    {
        $model = $this->resolveModelClassToInstance($model);

        if (! $model instanceof Model) {
            return null;
        }

        $table = $model->getTable();

        if (! is_string($table)) {
            return null;
        }

        return $table;
    }

    /**
     * Returns the primary key for a model
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws ReflectionException
     */
    public function getPrimaryKey(string|ObjectType $model): ?string
    {
        $model = $this->resolveModelClassToInstance($model);

        if (! $model instanceof Model) {
            return null;
        }

        $keyName = $model->getKeyName();

        if (! is_string($keyName)) {
            return null;
        }

        return $keyName;
    }

    /**
     * @param  class-string<Model>|ObjectType  $model
     */
    public function isQueryScopeOnModel(string|ObjectType $model, string $scopeName, Scope $scope): bool
    {
        if (! is_string($model)) {
            /** @var class-string<Model> $model */
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        if ($classReflection->hasMethod('scope' . ucfirst($scopeName))) {
            return true;
        }

        if (! $classReflection->hasMethod($scopeName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getMethod($scopeName, $scope);

        return $this->usesScopeAttribute($extendedMethodReflection);
    }

    /**
     * @param  class-string<Model>|ObjectType  $model
     */
    public function isRelationshipOnModel(string|ObjectType $model, string $relationName, Scope $scope): bool
    {
        if (! is_string($model)) {
            /** @var class-string<Model> $model */
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        if (! $classReflection->hasMethod($relationName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getMethod($relationName, $scope);

        foreach ($extendedMethodReflection->getVariants() as $extendedParametersAcceptor) {
            $returnType = $extendedParametersAcceptor->getReturnType();

            if ($returnType->isObject()->maybe()) {
                continue;
            }

            if (self::relationType()->isSuperTypeOf($returnType)->yes()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the class method returns the result of a Laravel Model relationship
     */
    public function classMethodReturnsRelationship(ClassMethod $classMethod): ?string
    {
        SimpleCallableNodeTraverser::traverse($classMethod->stmts, function (Node $node) use (&$found): ?int {
            // make sure we don't traverse into closures and such which might have a return statement
            if ($node instanceof FunctionLike) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if ($node instanceof Return_ && $node->expr instanceof MethodCall) {

                $methodCall = $node->expr;

                if ($methodCall->var instanceof Variable && $this->nodeNameResolver->isName($methodCall->var, 'this')) {

                    $foundRelationship = match ($this->nodeNameResolver->getName($methodCall->name)) {
                        'hasOne', 'hasOneThrough', 'hasMany', 'hasManyThrough', 'belongsToMany', 'morphOne', 'morphMany', 'morphToMany', 'morphedByMany' => $methodCall->name,
                        default => null,
                    };

                    if ($foundRelationship instanceof Identifier) {
                        $found = $foundRelationship->name;
                    }

                    return NodeVisitor::STOP_TRAVERSAL;
                }
            }

            return null;
        });

        return $found;
    }

    private function usesScopeAttribute(ExtendedMethodReflection $extendedMethodReflection): bool
    {
        foreach ($extendedMethodReflection->getAttributes() as $attributeReflection) {
            if ($attributeReflection->getName() === 'Illuminate\Database\Eloquent\Attributes\Scope') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the ClassReflectionFor the Model
     *
     * @param  class-string<Model>  $class
     *
     * @throws InvalidArgumentException
     */
    private function getClass(string $class): ClassReflection
    {
        if (! $this->reflectionProvider->hasClass($class)) {
            throw new InvalidArgumentException('Class not found');
        }

        $classReflection = $this->reflectionProvider->getClass($class);

        if (! $classReflection->isClass()) {
            throw new InvalidArgumentException('Class string does not resolve to class');
        }

        if (! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass(Model::class))) {
            throw new InvalidArgumentException('Class is not subclass of Model');
        }

        return $classReflection;
    }

    /**
     * Create an instance of the Model to interact with
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws ReflectionException
     */
    private function resolveModelClassToInstance(string|ObjectType $model): ?Model
    {
        $classReflection = is_string($model)
            ? $this->getClass($model)
            : $model->getObjectClassReflections()[0];

        if ($classReflection->isAbstract()) {
            return null;
        }

        try {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstance();
        } catch (Throwable) {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();
        }

        return $instance;
    }
}
