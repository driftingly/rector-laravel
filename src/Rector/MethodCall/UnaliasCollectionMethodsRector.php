<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\UnaliasCollectionMethodsRector\UnaliasCollectionMethodsRectorTest
 */
final class UnaliasCollectionMethodsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Use the base collection methods instead of their aliases.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->average();
$collection->some(fn (?int $number): bool => is_null($number));
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->avg();
$collection->contains(fn (?int $number): bool => is_null($number));
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
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        return $this->updateMethodCall($node);
    }

    private function updateMethodCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Support\Enumerable'))) {
            return null;
        }

        $name = $methodCall->name;
        if ($this->isName($name, 'some')) {
            $replacement = 'contains';
        } elseif ($this->isName($name, 'average')) {
            $replacement = 'avg';
        } else {
            return null;
        }

        $methodCall->name = new Identifier($replacement);

        return $methodCall;
    }
}
