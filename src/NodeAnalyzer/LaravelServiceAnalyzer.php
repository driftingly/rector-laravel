<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use PHPStan\Type\ObjectType;
use Rector\PHPStan\ScopeFetcher;
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
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver,
        private \PHPStan\Reflection\ReflectionProvider $reflectionProvider
    )
    {
    }

    public function defineServices(array $services, bool $merge = false): static
    {
        if ($merge) {
            $this->services = array_merge($this->services, $services);
        } else {
            $this->services = $services;
        }

        return $this;
    }

    public function getFacadeOrigin(StaticCall $node): ?ObjectType
    {
        $classType = $this->nodeTypeResolver->getType($node->class);
        $className = $node->class->name;

        if ($classType->hasMethod('getFacadeAccessor')->no()) {
            return null;
        }

        $reflection = new ReflectionMethod($className, 'getFacadeAccessor');

        if (! $reflection->isStatic() || $reflection->getNumberOfParameters() > 0) {
            return null;
        }
        $reflection->setAccessible(true);
        $origin = $reflection->invoke(null);

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

            if ($facadeOriginObjectType === null) {
                return false;
            }

            return $objectType->isSuperTypeOf($facadeOriginObjectType)->yes();
        } elseif ($node instanceof StaticCall) {
            return false;
        }

        return $this->nodeTypeResolver->isObjectType($node->var, $objectType);
    }

    public function isFacadeCall(StaticCall $node): bool
    {
        return $this->nodeTypeResolver->isObjectType(
            $node->class,
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
