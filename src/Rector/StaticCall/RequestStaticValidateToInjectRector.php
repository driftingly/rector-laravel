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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
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
    private $requestObjectTypes = [];

    /**
     * @readonly
     * @var \Rector\Core\Reflection\ReflectionResolver
     */
    private $reflectionResolver;

    public function __construct(ReflectionResolver $reflectionResolver)
    {
        $this->reflectionResolver = $reflectionResolver;
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
        return [StaticCall::class, FuncCall::class];
    }

    /**
     * @param  StaticCall|FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $requestParam = $this->addRequestParameterIfMissing($node, new ObjectType('Illuminate\Http\Request'));

        if (! $requestParam instanceof Param) {
            return null;
        }

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

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\FuncCall $node
     */
    private function shouldSkip($node): bool
    {
        if ($node instanceof StaticCall) {
            return ! $this->nodeTypeResolver->isObjectTypes($node->class, $this->requestObjectTypes);
        }

        $class = $this->betterNodeFinder->findParentType($node, Class_::class);
        if (! $class instanceof Class_) {
            return true;
        }

        $classMethod = $this->betterNodeFinder->findParentType($node, ClassMethod::class);
        if ($classMethod instanceof ClassMethod) {
            $classMethodReflection = $this->reflectionResolver->resolveMethodReflectionFromClassMethod($classMethod);
            $classMethodNamespaceName = ($getDeclaringClass = ($getPrototype = ($classMethodReflection2 = $classMethodReflection) ? $classMethodReflection2->getPrototype() : null) ? $getPrototype->getDeclaringClass() : null) ? $getDeclaringClass->getName() : null;
            $classNamespaceName = ($classNamespacedName = $class->namespacedName) ? $classNamespacedName->toString() : null;
            if ($classMethodNamespaceName !== $classNamespaceName) {
                return true;
            }
        }

        return ! $this->isName($node, 'request');
    }

    private function addRequestParameterIfMissing(Node $node, ObjectType $objectType): ?Param
    {
        $classMethod = $this->betterNodeFinder->findParentType($node, ClassMethod::class);

        if (! $classMethod instanceof ClassMethod) {
            return null;
        }

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
