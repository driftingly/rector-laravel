<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use ReflectionMethod;

final class LaravelServiceAnalyzer
{
    /**
     * @var array<string, class-string>
     */
    public array $services = [
        'db' => 'Illuminate\Database\DatabaseManager',
    ];

    public function __construct(
        private readonly NodeTypeResolver $nodeTypeResolver,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    /**
     * @param  array<string, class-string>  $services
     */
    public function defineServices(array $services, bool $merge = false): static
    {
        $this->services = $merge ? array_merge($this->services, $services) : $services;

        return $this;
    }

    public function getFacadeOrigin(StaticCall $staticCall): ?ObjectType
    {
        $classType = $this->nodeTypeResolver->getType($staticCall->class);
        $className = $this->nodeNameResolver->getName($staticCall->class);
        if (! is_string($className)) {
            return null;
        }

        if ($classType->hasMethod('getFacadeAccessor')->no()) {
            return null;
        }

        $reflectionMethod = new ReflectionMethod($className, 'getFacadeAccessor');

        if (! $reflectionMethod->isStatic() || $reflectionMethod->getNumberOfParameters() > 0) {
            return null;
        }
        $reflectionMethod->setAccessible(true);
        $origin = $reflectionMethod->invoke(null);
        if (! is_string($origin)) {
            return null;
        }

        if ($this->reflectionProvider->hasClass($origin)) {
            return new ObjectType($origin);
        }

        $service = $this->resolveServiceToClass($origin);

        if ($service === null) {
            return null;
        }

        return new ObjectType($service);
    }

    public function isMatchingCall(MethodCall|StaticCall $node, ObjectType $objectType, string $method): bool
    {
        if (! $this->nodeNameResolver->isName($node->name, $method)) {
            return false;
        }

        if ($node instanceof StaticCall && $this->isFacadeCall($node)) {
            $facadeOriginObjectType = $this->getFacadeOrigin($node);

            if (! $facadeOriginObjectType instanceof Type || $facadeOriginObjectType->isObject()->no()) {
                return false;
            }

            return $objectType->isSuperTypeOf($facadeOriginObjectType)->yes();
        } elseif ($node instanceof StaticCall) {
            return false;
        }

        return $this->nodeTypeResolver->isObjectType($node->var, $objectType);
    }

    public function isFacadeCall(StaticCall $staticCall): bool
    {
        return $this->nodeTypeResolver->isObjectType(
            $staticCall->class,
            new ObjectType('Illuminate\Support\Facades\Facade')
        );
    }

    /**
     * @return class-string|null
     */
    protected function resolveServiceToClass(string $service): ?string
    {
        return $this->services[$service] ?? null;
    }
}
