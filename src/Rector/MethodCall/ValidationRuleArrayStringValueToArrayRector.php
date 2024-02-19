<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ValidationRuleArrayStringValueToArrayRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert string validation rules into arrays for Laravel\'s Validator::make.',
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
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        if (
            ! $this->isName($node->name, 'make') &&
            $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Validation\Factory')
            )
        ) {
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

        $changed = false;

        foreach ($rulesArgument->items as $item) {
            if ($item instanceof ArrayItem && $item->value instanceof String_) {
                $stringRules = $item->value->value;
                $arrayRules = explode('|', $stringRules);
                $item->value = new Array_(array_map(static fn ($rule) => new ArrayItem(new String_($rule)), $arrayRules));
                $changed = true;
            }
        }

        if ($changed) {
            return $node;
        }

        return null;
    }
}
