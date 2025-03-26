<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

final class InvokableRuleToValidationRuleRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Class_
    {
        if ($node->isAbstract()) {
            return null;
        }

        $found = false;
        $conflict = false;

        foreach ($node->implements as $implement) {
            if ($this->isObjectType($implement, new ObjectType('Illuminate\Contracts\Validation\InvokableRule'))) {
                $found = true;
            }
            if ($this->isObjectType($implement, new ObjectType('Illuminate\Contracts\Validation\ValidationRule'))) {
                $conflict = true;
            }
        }

        if (! $found || $conflict) {
            return null;
        }

        $node->implements[] = new FullyQualified('Illuminate\Contracts\Validation\ValidationRule');
        $node->implements = array_filter(
            $node->implements,
            fn (Name $name) => ! $this->isName($name, 'Illuminate\Contracts\Validation\InvokableRule')
        );

        foreach ($node->getMethods() as $classMethod) {
            if ($this->isName($classMethod->name, '__invoke')) {
                $classMethod->name = new Identifier('validate');
            }
        }

        return $node;
    }
}
