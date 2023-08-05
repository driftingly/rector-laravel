<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Rector\Core\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/27276
 * @see \RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\RequestStaticValidateToInjectRectorTest
 */
final class RequestStaticValidateToInjectRector extends AbstractScopeAwareRector
{
    /**
     * @var ObjectType[]
     */
    private array $requestObjectTypes = [];

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver
    ) {
        $this->requestObjectTypes = [new ObjectType('Illuminate\Http\Request'), new ObjectType('Request')];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change static validate() method to $request->validate()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

class SomeClass
{
    public function store()
    {
        $validatedData = Request::validate(['some_attribute' => 'required']);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

class SomeClass
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validatedData = $request->validate(['some_attribute' => 'required']);
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        /** @var ClassMethod $node */
        $this->traverseNodesWithCallable((array) $node->stmts, function (Node $subNode) use ($node, $scope): ?Node {
            if (! $subNode instanceof StaticCall && ! $subNode instanceof FuncCall) {
                return null;
            }

            if ($this->shouldSkip($node, $subNode, $scope)) {
                return null;
            }

            $requestParam = $this->addRequestParameterIfMissing($node, new ObjectType('Illuminate\Http\Request'));

            $methodName = $this->getName($subNode->name);

            if ($methodName === null) {
                return null;
            }

            if ($subNode instanceof FuncCall) {
                if ($subNode->args === []) {
                    return $requestParam->var;
                }

                $methodName = 'input';
            }

            return new MethodCall($requestParam->var, new Identifier($methodName), $subNode->args);
        });

        return null;
    }

    private function shouldSkip(ClassMethod $classMethod, StaticCall|FuncCall $node, Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();

        if (! $classReflection instanceof ClassReflection || ! $classReflection->isClass()) {
            return true;
        }

        if ($node instanceof StaticCall) {
            return ! $this->nodeTypeResolver->isObjectTypes($node->class, $this->requestObjectTypes);
        }

        $classMethodReflection = $this->reflectionResolver->resolveMethodReflectionFromClassMethod(
            $classMethod,
            $scope
        );
        $classMethodNamespaceName = $classMethodReflection?->getPrototype()?->getDeclaringClass()?->getName();
        if ($classMethodNamespaceName !== $classReflection->getName()) {
            return true;
        }

        return ! $this->isName($node, 'request');
    }

    private function addRequestParameterIfMissing(ClassMethod $classMethod, ObjectType $objectType): Param
    {
        foreach ($classMethod->params as $paramNode) {
            if (! $this->nodeTypeResolver->isObjectType($paramNode, $objectType)) {
                continue;
            }

            return $paramNode;
        }

        $classMethod->params[] = $paramNode = new Param(new Variable(
            'request'
        ), null, new FullyQualified($objectType->getClassName()));

        return $paramNode;
    }
}
