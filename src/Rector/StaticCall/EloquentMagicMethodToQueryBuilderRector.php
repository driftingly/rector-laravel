<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\EloquentMagicMethodToQueryBuilderRectorTest;
use Webmozart\Assert\Assert;

/**
 * @see EloquentMagicMethodToQueryBuilderRectorTest
 */
final class EloquentMagicMethodToQueryBuilderRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const string EXCLUDE_METHODS = 'exclude_methods';

    public const string REMOVE_QUERY_FOR_EXCLUDED_METHODS = 'remove_query_for_excluded_methods';

    /**
     * @var string[]
     */
    private array $excludeMethods = [];

    private bool $removeQueryForExcludedMethods = false;

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
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::first();
$user = User::query()->find(1);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::query()->first();
$user = User::find(1);
CODE_SAMPLE
                    , [
                        self::EXCLUDE_METHODS => ['find'],
                        self::REMOVE_QUERY_FOR_EXCLUDED_METHODS => true,
                    ]),
            ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, MethodCall::class];
    }

    /**
     * @param  StaticCall|MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->removeQueryForExcludedMethod($node);
        }

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

        $classReflection = $this->resolveModelReflection($node->class);

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
        $removeQueryForExcludedMethods = $configuration[self::REMOVE_QUERY_FOR_EXCLUDED_METHODS] ?? false;
        Assert::boolean($removeQueryForExcludedMethods);
        unset($configuration[self::REMOVE_QUERY_FOR_EXCLUDED_METHODS]);

        $excludeMethods = $configuration[self::EXCLUDE_METHODS] ?? $configuration;
        Assert::isArray($excludeMethods);
        Assert::allString($excludeMethods);

        $this->excludeMethods = $excludeMethods;
        $this->removeQueryForExcludedMethods = $removeQueryForExcludedMethods;
    }

    private function removeQueryForExcludedMethod(MethodCall $methodCall): ?StaticCall
    {
        if (! $this->removeQueryForExcludedMethods
            || ! $methodCall->name instanceof Identifier
            || ! in_array($methodCall->name->toString(), $this->excludeMethods, true)
        ) {
            return null;
        }

        $query = $methodCall->var;
        if (! $query instanceof StaticCall
            || ! $this->isName($query->name, 'query')
            || $query->args !== []
            || ! $query->class instanceof Name
            || $query->class->isSpecialClassName()
        ) {
            return null;
        }

        $classReflection = $this->resolveModelReflection($query->class);

        if (! $classReflection instanceof ClassReflection
            || $classReflection->getNativeMethod('query')->getDeclaringClass()->getName() !== Model::class
            || ! $this->isMagicMethod($classReflection, $methodCall->name->toString())
        ) {
            return null;
        }

        return new StaticCall($query->class, $methodCall->name, $methodCall->args);
    }

    private function resolveModelReflection(Expr|Name $class): ?ClassReflection
    {
        $resolvedType = $this->nodeTypeResolver->getType($class);

        $classNames = $resolvedType->isClassString()->yes()
            ? $resolvedType->getClassStringObjectType()->getObjectClassNames()
            : $resolvedType->getObjectClassNames();

        foreach ($classNames as $className) {
            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);

            if ($classReflection->is(Model::class)) {
                return $classReflection;
            }
        }

        return null;
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
