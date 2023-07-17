<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/27276
 * @see \RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\RequestStaticValidateToInjectRectorTest
 */
final class RequestStaticValidateToInjectRector extends AbstractRector
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

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($node);

        if (! $classReflection instanceof ClassReflection || ! $classReflection->isClass()) {
            return null;
        }

        foreach ((array) $node->stmts as $stmt) {
            $staticFuncCall = $this->findRequestUsage($stmt);

            if ($staticFuncCall === null) {
                continue;
            }

            if ($this->shouldSkip($node, $staticFuncCall, $classReflection->getName())) {
                continue;
            }

            $result = $this->createMethodCallFromStaticCallOrFuncCall($node, $staticFuncCall);

            if ($result === null) {
                continue;
            }

            /** @var Expression $stmt */
            $this->replace($stmt, $result);
        }

        return $node;
    }

    private function replace(Expression $stmt, Error|MethodCall|Variable $result): void
    {
        if ($result instanceof MethodCall) {
            if ($stmt->expr instanceof Assign) {
                $stmt->expr->expr = $result;
            } else {
                $stmt->expr = $result;
            }
        }

        if ($result instanceof Variable) {
            if ($stmt->expr instanceof Assign) {
                if ($stmt->expr->expr instanceof MethodCall) {
                    $stmt->expr->expr->var = $result;
                } else {
                    $stmt->expr->expr = $result;
                }
            } elseif ($stmt->expr instanceof MethodCall) {
                $stmt->expr->var = $result;
            } else {
                $stmt->expr = $result;
            }
        }
    }

    private function shouldSkip(ClassMethod $classMethod, StaticCall|FuncCall $node, string $className): bool
    {
        if ($node instanceof StaticCall) {
            return ! $this->nodeTypeResolver->isObjectTypes($node->class, $this->requestObjectTypes);
        }

        $classMethodReflection = $this->reflectionResolver->resolveMethodReflectionFromClassMethod($classMethod);
        $classMethodNamespaceName = $classMethodReflection?->getPrototype()?->getDeclaringClass()?->getName();

        if ($classMethodNamespaceName !== $className) {
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

    private function createMethodCallFromStaticCallOrFuncCall(
        ClassMethod $classMethod,
        StaticCall|FuncCall $node
    ): Variable|MethodCall|Error|null {
        $requestParam = $this->addRequestParameterIfMissing($classMethod, new ObjectType('Illuminate\Http\Request'));

        $methodName = $this->getName($node->name);

        if ($methodName === null) {
            return null;
        }

        if ($node instanceof FuncCall) {
            if ($node->args === []) {
                return $requestParam->var;
            }

            $methodName = 'input';
        }

        return new MethodCall($requestParam->var, new Identifier($methodName), $node->args);
    }

    private function findRequestUsage(Stmt $stmt): StaticCall|FuncCall|null
    {
        if (! $stmt instanceof Expression) {
            return null;
        }

        if ($stmt->expr instanceof Assign) {
            return $this->findStaticCallOrFuncCall($stmt->expr->expr);
        }

        return $this->findStaticCallOrFuncCall($stmt->expr);
    }

    private function findStaticCallOrFuncCall(Expr $staticFuncCall): FuncCall|StaticCall|null
    {
        if ($staticFuncCall instanceof FuncCall || $staticFuncCall instanceof StaticCall) {
            return $staticFuncCall;
        }

        if ($staticFuncCall instanceof MethodCall && $staticFuncCall->var instanceof FuncCall) {
            return $staticFuncCall->var;
        }

        return null;
    }
}
