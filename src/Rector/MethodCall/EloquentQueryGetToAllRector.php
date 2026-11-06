<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\EloquentQueryGetToAllRectorTest;

/**
 * @see EloquentQueryGetToAllRectorTest
 */
final class EloquentQueryGetToAllRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes `Model::query()->get()` to `Model::all()`',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use App\Models\User;

$users = User::query()->get();
$names = User::query()->get(['name']);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use App\Models\User;

$users = User::all();
$names = User::all(['name']);
CODE_SAMPLE
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
        if (! $this->isName($node->name, 'get')) {
            return null;
        }

        $query = $node->var;
        if (! $query instanceof StaticCall
            || ! $this->isName($query->name, 'query')
            || $query->args !== []
            || ! $query->class instanceof Name
            || $this->isNames($query->class, ['self', 'parent'])
        ) {
            return null;
        }

        $modelReflection = $this->resolveModelReflection($query->class);
        if (! $modelReflection instanceof ClassReflection
            || ! $this->isDeclaredByModel($modelReflection, 'query')
            || ! $this->isDeclaredByModel($modelReflection, 'all')
        ) {
            return null;
        }

        return new StaticCall($query->class, new Identifier('all'), $node->args);
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

    private function isDeclaredByModel(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->hasNativeMethod($methodName)
            && $classReflection->getNativeMethod($methodName)->getDeclaringClass()->getName() === Model::class;
    }
}
