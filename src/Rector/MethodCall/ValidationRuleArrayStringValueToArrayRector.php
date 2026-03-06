<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\ValidationRuleArrayStringValueToArrayRectorTest
 */
class ValidationRuleArrayStringValueToArrayRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert string validation rules into arrays for Laravel\'s Validator.',
            [
                new CodeSample(
                    // Code before
                    <<<'CODE_SAMPLE'
Validator::make($data, [
    'field' => 'required|nullable|string|max:255',
]);
CODE_SAMPLE
                    ,
                    // Code after
                    <<<'CODE_SAMPLE'
Validator::make($data, [
    'field' => ['required', 'nullable', 'string', 'max:255'],
]);
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class, ClassLike::class];
    }

    /**
     * @param  MethodCall|StaticCall|ClassLike  $node
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Stmt\ClassLike|null
     */
    public function refactor(Node $node)
    {
        if ($node instanceof ClassLike) {
            return $this->refactorClassMethod($node);
        }

        if ($node instanceof MethodCall && $this->isName($node->name, 'validate')) {
            return $this->refactorValidateCall($node);
        }

        return $this->refactorCall($node);
    }

    private function processValidationRules(Array_ $array): bool
    {
        $changed = false;

        foreach ($array->items as $item) {
            if ($item instanceof ArrayItem) {
                if ($item->value instanceof String_) {
                    $item->value = $this->processStringRule($item->value);
                    $changed = true;
                } elseif ($item->value instanceof InterpolatedString) {
                    $item->value = $this->processInterpolatedStringRule($item->value);
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    private function processStringRule(String_ $string): Array_
    {
        $stringRules = $string->value;
        $newRules = explode('|', $stringRules);

        return new Array_(array_map(static fn (string $rule) => new ArrayItem(new String_($rule)), $newRules));
    }

    private function processInterpolatedStringRule(InterpolatedString $interpolatedString): Array_
    {
        // Build rule parts by splitting on pipes while preserving variables
        $allRuleParts = [];
        $currentParts = [];

        foreach ($interpolatedString->parts as $part) {
            if ($part instanceof InterpolatedStringPart) {
                $segments = explode('|', $part->value);
                foreach ($segments as $index => $segment) {
                    // Complete current rule and start new one
                    if ($index !== 0 && $currentParts !== []) {
                        $allRuleParts[] = $currentParts;
                        $currentParts = [];
                    }

                    if ($segment !== '') {
                        $currentParts[] = new InterpolatedStringPart($segment);
                    }
                }
            } else {
                $currentParts[] = $part; // Variables and other parts
            }
        }

        if ($currentParts !== []) {
            $allRuleParts[] = $currentParts;
        }

        return new Array_(array_map(fn ($parts) => new ArrayItem(new InterpolatedString($parts)), $allRuleParts));
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall $node
     * @return \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall|null
     */
    private function refactorCall($node)
    {
        if (
            ! $this->isName($node->name, 'make')
        ) {
            return null;
        }

        if (
            $node instanceof MethodCall &&
            ! $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Validation\Factory')
            )) {
            return null;
        }

        if (
            $node instanceof StaticCall &&
            ! $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Support\Facades\Validator')
            )) {
            return null;
        }

        if (count($node->args) !== 2) {
            return null;
        }
        if (! $node->args[1] instanceof Arg) {
            return null;
        }

        // The second argument should be the rules array
        $rulesArgument = $node->args[1]->value;

        if (! $rulesArgument instanceof Array_) {
            return null;
        }

        return $this->processValidationRules($rulesArgument) ? $node : null;
    }

    private function refactorValidateCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Http\Request'))) {
            return null;
        }

        if ($methodCall->args === []) {
            return null;
        }

        if (! $methodCall->args[0] instanceof Arg) {
            return null;
        }

        // The first argument should be the rules array
        $rulesArgument = $methodCall->args[0]->value;

        if (! $rulesArgument instanceof Array_) {
            return null;
        }

        return $this->processValidationRules($rulesArgument) ? $methodCall : null;
    }

    private function refactorClassMethod(ClassLike $classLike): ?ClassLike
    {
        if (! $this->isObjectType($classLike, new ObjectType('Illuminate\Foundation\Http\FormRequest'))) {
            return null;
        }

        $hasChanged = false;
        foreach ($classLike->getMethods() as $classMethod) {
            if (! $this->isName($classMethod, 'rules')) {
                continue;
            }

            $changed = false;
            $this->traverseNodesWithCallable($classMethod, function (Node $node) use (&$changed, &$hasChanged) {
                if ($changed) {
                    $hasChanged = true;

                    return NodeVisitor::STOP_TRAVERSAL;
                }

                if (! $node instanceof Return_) {
                    return null;
                }

                if (! $node->expr instanceof Array_) {
                    return null;
                }

                if ($this->processValidationRules($node->expr)) {
                    $hasChanged = true;
                    $changed = true;

                    return $node;
                }

                return null;
            });
        }

        if ($hasChanged) {
            return $classLike;
        }

        return null;
    }
}
