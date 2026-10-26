<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\Array_ as ArrayCast;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_ as ScalarInt;
use PhpParser\Node\Scalar\String_ as ScalarString;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\RequestInputToTypedMethodRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RequestInputToTypedMethodRectorTest
 */
final class RequestInputToTypedMethodRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace explicit string, integer, float and array casts of Request input() with typed accessors, preserving scalar strings. Leave uncast values, boolean casts and other input sources unchanged.',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$name = (string) $request->input('name');
$age = (int) $request->input('age');
$price = (float) $request->input('price');
$tags = (array) $request->input('tags');
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$name = $request->string('name')->toString();
$age = $request->integer('age');
$price = $request->float('price');
$tags = $request->array('tags');
CODE_SAMPLE
            )]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Cast::class];
    }

    /** @param Cast $node */
    public function refactor(Node $node): ?Node
    {
        $typedMethod = match (true) {
            $node instanceof String_ => 'string',
            $node instanceof Int_ => 'integer',
            $node instanceof Double => 'float',
            $node instanceof ArrayCast => 'array',
            $node instanceof Bool_ => 'boolean',
            default => null,
        };
        if ($typedMethod === null) {
            return null;
        }

        $expr = $node->expr;
        if ($expr instanceof Coalesce && $expr->right instanceof ConstFetch && $this->isName($expr->right, 'null')) {
            $expr = $expr->left;
        }

        if (! $expr instanceof MethodCall || $expr->isFirstClassCallable()) {
            return null;
        }

        if (! $this->isObjectType($expr->var, new ObjectType('Illuminate\Http\Request'))) {
            return null;
        }

        // A matching typed accessor already performs the cast, except string() returns Stringable.
        if ($this->isName($expr->name, $typedMethod)) {
            return $typedMethod === 'string' ? new MethodCall($expr, 'toString') : $expr;
        }

        // boolean() parses strings such as "false", unlike PHP's boolean cast.
        // get(), magic properties and offsets also read different sources from input().
        if ($typedMethod === 'boolean' || ! $this->isName($expr->name, 'input')) {
            return null;
        }

        $args = $expr->args;
        if (count($args) < 1 || count($args) > 2) {
            return null;
        }

        foreach ($args as $arg) {
            if (! $arg instanceof Arg || $arg->unpack || $arg->name instanceof Identifier) {
                return null;
            }
        }

        if (! $args[0]->value instanceof ScalarString) {
            return null;
        }

        if (isset($args[1]) && $this->isAccessorDefault($args[1], $typedMethod)) {
            unset($args[1]);
        }

        // array() accepts only a key; a non-empty default cannot be forwarded.
        if ($typedMethod === 'array' && isset($args[1])) {
            return null;
        }

        $expr->name = new Identifier($typedMethod);
        $expr->args = $args;

        return $typedMethod === 'string' ? new MethodCall($expr, 'toString') : $expr;
    }

    private function isAccessorDefault(Arg $arg, string $typedMethod): bool
    {
        $value = $arg->value;

        return match ($typedMethod) {
            'string' => $value instanceof ScalarString && $value->value === '',
            'integer' => $value instanceof ScalarInt && $value->value === 0,
            'float' => ($value instanceof ScalarInt || $value instanceof Float_) && (float) $value->value === 0.0,
            'array' => $value instanceof Array_ && $value->items === [],
            default => false,
        };
    }
}
