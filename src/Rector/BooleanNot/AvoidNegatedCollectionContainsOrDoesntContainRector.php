<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\BooleanNot;

use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector\AvoidNegatedCollectionContainsOrDoesntContainRectorTest
 */
final class AvoidNegatedCollectionContainsOrDoesntContainRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert negated calls to `contains` to `doesntContain`, or vice versa.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
! $collection->contains(fn (?int $number): bool => is_null($number));
! $collection->doesntContain(fn (?int $number) => $number > 0);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->doesntContain(fn (?int $number): bool => is_null($number));
$collection->contains(fn (?int $number) => $number > 0);
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
        return [BooleanNot::class];
    }

    /**
     * @param  BooleanNot  $node
     */
    public function refactor(Node $node): ?Node
    {
        return $this->updateBooleanNot($node);
    }

    private function updateBooleanNot(BooleanNot $booleanNot): ?MethodCall
    {
        $expr = $booleanNot->expr;
        if (! $expr instanceof MethodCall) {
            return null;
        }

        if (! $this->isObjectType($expr->var, new ObjectType('Illuminate\Support\Enumerable'))) {
            return null;
        }

        $name = $expr->name;
        if ($this->isName($name, 'contains')) {
            $replacement = 'doesntContain';
        } elseif ($this->isName($name, 'doesntContain')) {
            $replacement = 'contains';
        } else {
            return null;
        }

        $expr->name = new Identifier($replacement);

        return $expr;
    }
}
