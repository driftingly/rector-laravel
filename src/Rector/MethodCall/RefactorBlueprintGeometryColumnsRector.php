<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector\RefactorBlueprintGeometryColumnsRectorTest
 */
class RefactorBlueprintGeometryColumnsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'refactors calls with the pre Laravel 11 methods for blueprint geometry columns',
            [new CodeSample(<<<'CODE_SAMPLE'
$blueprint->point('coordinates')->spatialIndex();
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$blueprint->geometry('coordinates', 'point')->spatialIndex();
CODE_SAMPLE
)]
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
        if (! $this->isNames($node->name, [
            'point',
            'linestring',
            'polygon',
            'geometrycollection',
            'multipoint',
            'multilinestring',
            'multipolygon',

        ])) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Schema\Blueprint'))) {
            return null;
        }

        $previousName = $node->name;

        $node->name = new Identifier('geometry');
        $node->args[] = new Arg(new String_($previousName->name));

        return $node;
    }
}
