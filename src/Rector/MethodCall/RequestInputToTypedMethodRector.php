<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_ as ScalarString;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\RequestInputToTypedMethodRectorTest
 */
final class RequestInputToTypedMethodRector extends AbstractRector
{
    private const array GENERIC_METHODS = ['input', 'get', 'data'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Refactor Request input/get/data methods and array access to type-specific methods when the type is known',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$name = $request->input('name');
$age = (int) $request->get('age');
$price = (float) $request->data('price');
$isActive = (bool) $request['is_active'];
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$name = $request->string('name');
$age = $request->integer('age');
$price = $request->float('price');
$isActive = $request->boolean('is_active');
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
        return [Cast::class, Assign::class];
    }

    /**
     * @param  Cast|Assign  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Cast) {
            return $this->refactorCast($node);
        }

        if ($node instanceof Assign) {
            return $this->refactorAssign($node);
        }

        return null;
    }

    private function refactorCast(Cast $cast): ?Node
    {
        $expr = $cast->expr;

        if ($expr instanceof MethodCall) {
            $typedMethod = $this->getTypedMethodFromCast($cast);
            if ($typedMethod !== null && $this->isRequestMethodCall($expr)) {
                return $this->replaceWithTypedMethod($expr, $typedMethod);
            }
        }

        if ($expr instanceof ArrayDimFetch) {
            $typedMethod = $this->getTypedMethodFromCast($cast);
            if ($typedMethod !== null && $this->isRequestArrayAccess($expr)) {
                return $this->convertArrayAccessToTypedMethod($expr, $typedMethod);
            }
        }

        if ($expr instanceof PropertyFetch) {
            $typedMethod = $this->getTypedMethodFromCast($cast);
            if ($typedMethod !== null && $this->isRequestPropertyFetch($expr)) {
                return $this->convertPropertyFetchToTypedMethod($expr, $typedMethod);
            }
        }

        return null;
    }

    private function refactorAssign(Assign $assign): ?Node
    {
        $expr = $assign->expr;

        if ($expr instanceof MethodCall && $this->isRequestMethodCall($expr)) {
            $typedMethod = $this->inferTypeFromContext($assign);
            if ($typedMethod !== null) {
                $assign->expr = $this->replaceWithTypedMethod($expr, $typedMethod);

                return $assign;
            }
        }

        if ($expr instanceof ArrayDimFetch && $this->isRequestArrayAccess($expr)) {
            $typedMethod = $this->inferTypeFromContext($assign);
            if ($typedMethod !== null) {
                $assign->expr = $this->convertArrayAccessToTypedMethod($expr, $typedMethod);

                return $assign;
            }
        }

        if ($expr instanceof PropertyFetch && $this->isRequestPropertyFetch($expr)) {
            $typedMethod = $this->inferTypeFromContext($assign);
            if ($typedMethod !== null) {
                $assign->expr = $this->convertPropertyFetchToTypedMethod($expr, $typedMethod);

                return $assign;
            }
        }

        return null;
    }

    private function isRequestMethodCall(MethodCall $methodCall): bool
    {
        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Http\Request'))) {
            return false;
        }

        $methodName = $this->getName($methodCall->name);

        return $methodName !== null && in_array($methodName, self::GENERIC_METHODS, true);
    }

    private function isRequestArrayAccess(ArrayDimFetch $arrayDimFetch): bool
    {
        return $arrayDimFetch->var instanceof Variable
            && $this->isObjectType($arrayDimFetch->var, new ObjectType('Illuminate\Http\Request'));
    }

    private function isRequestPropertyFetch(PropertyFetch $propertyFetch): bool
    {
        return $propertyFetch->var instanceof Variable
            && $this->isObjectType($propertyFetch->var, new ObjectType('Illuminate\Http\Request'));
    }

    private function getTypedMethodFromCast(Cast $cast): ?string
    {
        return match (true) {
            $cast instanceof String_ => 'string',
            $cast instanceof Int_ => 'integer',
            $cast instanceof Double => 'float',
            $cast instanceof Bool_ => 'boolean',
            default => null,
        };
    }

    private function inferTypeFromContext(Assign $assign): ?string
    {
        if (! $assign->var instanceof Variable) {
            return null;
        }

        $varType = $this->nodeTypeResolver->getType($assign->var);

        if ($varType->isString()->yes()) {
            return 'string';
        }

        if ($varType->isInteger()->yes()) {
            return 'integer';
        }

        if ($varType->isFloat()->yes()) {
            return 'float';
        }

        if ($varType->isBoolean()->yes()) {
            return 'boolean';
        }

        $objectClassNames = $varType->getObjectClassNames();
        if (in_array('Carbon\Carbon', $objectClassNames, true) || in_array('Illuminate\Support\Carbon', $objectClassNames, true)) {
            return 'date';
        }

        return null;
    }

    private function replaceWithTypedMethod(MethodCall $methodCall, string $typedMethod): MethodCall
    {
        $methodCall->name = new Identifier($typedMethod);

        return $methodCall;
    }

    private function convertArrayAccessToTypedMethod(ArrayDimFetch $arrayDimFetch, string $typedMethod): MethodCall
    {
        if (! $arrayDimFetch->var instanceof Variable) {
            return new MethodCall($arrayDimFetch->var, $typedMethod);
        }

        $args = [];
        if ($arrayDimFetch->dim instanceof Expr) {
            $args[] = new Arg($arrayDimFetch->dim);
        }

        return new MethodCall($arrayDimFetch->var, $typedMethod, $args);
    }

    private function convertPropertyFetchToTypedMethod(PropertyFetch $propertyFetch, string $typedMethod): MethodCall
    {
        $propertyName = $this->getName($propertyFetch->name);
        if ($propertyName === null) {
            return new MethodCall($propertyFetch->var, $typedMethod);
        }

        return new MethodCall(
            $propertyFetch->var,
            $typedMethod,
            [new Arg(new ScalarString($propertyName))]
        );
    }
}
