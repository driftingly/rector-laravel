<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ExtendedParameterReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\EloquentQueryToModelStaticMethodRectorTest;
use Webmozart\Assert\Assert;

/**
 * @see EloquentQueryToModelStaticMethodRectorTest
 */
final class EloquentQueryToModelStaticMethodRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const string METHODS = 'methods';

    /**
     * @var array<string, string>
     */
    private array $staticMethodsByBuilderMethod = [
        'get' => 'all',
        'with' => 'with',
        'useWritePdo' => 'onWriteConnection',
    ];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes `Model::query()->method()` to the static method on the model that wraps it',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use App\Models\User;

$users = User::query()->get();
$users = User::query()->with('posts')->where('active', true)->get();
$user = User::query()->useWritePdo()->find(1);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use App\Models\User;

$users = User::all();
$users = User::with('posts')->where('active', true)->get();
$user = User::onWriteConnection()->find(1);
CODE_SAMPLE,
                [self::METHODS => [
                    'get' => 'all',
                    'with' => 'with',
                    'useWritePdo' => 'onWriteConnection',
                ]],
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $staticMethod = $this->staticMethodsByBuilderMethod[$node->name->toString()] ?? null;
        if ($staticMethod === null) {
            return null;
        }

        $query = $node->var;
        if (! $query instanceof StaticCall
            || ! $this->isName($query->name, 'query')
            || $query->args !== []
            || ! $query->class instanceof Name
            || $query->class->isSpecialClassName()
        ) {
            return null;
        }

        $modelReflection = $this->resolveModelReflection($query->class);
        if (! $modelReflection instanceof ClassReflection) {
            return null;
        }

        $staticMethodReflection = $this->resolveOriginalPublicStaticMethod($modelReflection, $staticMethod);
        if (! $staticMethodReflection instanceof ExtendedMethodReflection
            || ! $this->acceptsArguments($staticMethodReflection, $node)
        ) {
            return null;
        }

        return new StaticCall($query->class, new Identifier($staticMethod), $node->args);
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $staticMethodsByBuilderMethod = $configuration[self::METHODS] ?? $configuration;
        Assert::isMap($staticMethodsByBuilderMethod);
        Assert::allString($staticMethodsByBuilderMethod);

        $this->staticMethodsByBuilderMethod = $staticMethodsByBuilderMethod;
    }

    private function resolveModelReflection(Name $class): ?ClassReflection
    {
        $classNames = $this->nodeTypeResolver->getType($class)->getObjectClassNames();
        if (count($classNames) !== 1) {
            return null;
        }

        $className = $classNames[0];
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        return $classReflection->is(Model::class)
            ? $classReflection
            : null;
    }

    private function resolveOriginalPublicStaticMethod(ClassReflection $classReflection, string $methodName): ?ExtendedMethodReflection
    {
        if (! $classReflection->hasNativeMethod($methodName)) {
            return null;
        }

        $methodReflection = $classReflection->getNativeMethod($methodName);
        $parentClass = $methodReflection->getDeclaringClass()->getParentClass();

        return $methodReflection->isPublic()
            && $methodReflection->isStatic()
            && (! $parentClass instanceof ClassReflection || ! $parentClass->hasNativeMethod($methodName))
                ? $methodReflection
                : null;
    }

    private function acceptsArguments(ExtendedMethodReflection $methodReflection, MethodCall $methodCall): bool
    {
        $parameters = $methodReflection->getOnlyVariant()->getParameters();
        $parameterNames = array_map(
            static fn (ExtendedParameterReflection $parameter): string => $parameter->getName(),
            $parameters,
        );
        $requiredParameters = array_filter(
            $parameters,
            static fn (ExtendedParameterReflection $parameter): bool => ! $parameter->isOptional(),
        );

        if (count($methodCall->args) > count($parameters)
            || count($methodCall->args) < count($requiredParameters)
        ) {
            return false;
        }

        foreach ($methodCall->args as $arg) {
            if (! $arg instanceof Arg || $arg->unpack) {
                return false;
            }

            if ($arg->name instanceof Identifier && ! in_array($arg->name->toString(), $parameterNames, true)) {
                return false;
            }
        }

        return true;
    }
}
