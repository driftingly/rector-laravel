<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\EloquentMagicMethodToQueryBuilderRectorTest
 */
final class EloquentMagicMethodToQueryBuilderRector extends AbstractRector implements ConfigurableRectorInterface
{
    final public const string EXCLUDE_METHODS = 'exclude_methods';

    /**
     * @var string[]
     */
    private array $excludeMethods = [];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'The EloquentMagicMethodToQueryBuilderRule is designed to automatically transform certain magic method calls on Eloquent Models into corresponding Query Builder method calls.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::first();
$user = User::find(1);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::query()->first();
$user = User::find(1);
CODE_SAMPLE
                    , [
                        self::EXCLUDE_METHODS => ['find'],
                    ]),

            ]);
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
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = $node->name->toString();

        if (
            $methodName === 'query' // short circuit
            || in_array($methodName, $this->excludeMethods, true)
        ) {
            return null;
        }

        $resolvedType = $this->nodeTypeResolver->getType($node->class);

        $classNames = $resolvedType->isClassString()->yes()
            ? $resolvedType->getClassStringObjectType()->getObjectClassNames()
            : $resolvedType->getObjectClassNames();

        $classReflection = null;

        foreach ($classNames as $className) {
            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);

            if ($classReflection->is(Model::class)) {
                break;
            }

            $classReflection = null;
        }

        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $this->isMagicMethod($classReflection, $methodName)) {
            return null;
        }

        return new MethodCall(new StaticCall($node->class, 'query'), $node->name, $node->args);
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $excludeMethods = $configuration[self::EXCLUDE_METHODS] ?? $configuration;
        Assert::isArray($excludeMethods);
        Assert::allString($excludeMethods);

        $this->excludeMethods = $excludeMethods;
    }

    private function isMagicMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (! $classReflection->hasNativeMethod($methodName)) {
            // if the class doesn't have the method then check if the method is a scope
            if ($classReflection->hasNativeMethod('scope' . ucfirst($methodName))) {
                return true;
            }

            // otherwise, need to check if the method is directly on the EloquentBuilder or QueryBuilder
            return $this->isPublicMethod(EloquentBuilder::class, $methodName)
                || $this->isPublicMethod(QueryBuilder::class, $methodName);
        }

        if (! $classReflection->hasMethod($methodName)) {
            return false; // no mixin
        }

        $extendedMethodReflection = $classReflection->getMethod($methodName, new OutOfClassScope);

        if (! $extendedMethodReflection->isPublic() || $extendedMethodReflection->isStatic()) {
            return false;
        }

        $declaringClass = $extendedMethodReflection->getDeclaringClass();

        // finally, make sure the method is on the builders or a subclass
        return $declaringClass->is(EloquentBuilder::class) || $declaringClass->is(QueryBuilder::class);
    }

    private function isPublicMethod(string $className, string $methodName): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (! $classReflection->hasNativeMethod($methodName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod($methodName);

        return $extendedMethodReflection->isPublic() && ! $extendedMethodReflection->isStatic();
    }
}
