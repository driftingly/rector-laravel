<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ConvertEnumerableToArrayToAllRector\ConvertEnumerableToArrayToAllRectorTest
 */
final class ConvertEnumerableToArrayToAllRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert `toArray()` to `all()` when the collection does not contain any Arrayable objects.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

new Collection([0, 1, -1])->toArray();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

new Collection([0, 1, -1])->all();
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
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  MethodCall|NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'toArray')) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Support\Enumerable'))) {
            return null;
        }

        $scope = ScopeFetcher::fetch($node);
        $type = TypeCombinator::removeNull(
            // This HAS to use the $scope->getType() as opposed to $this->getType()
            // because it's not getting the proper type from the Larastan extensions
            $scope->getType($node->var)
        );
        $valueType = $type->getTemplateType('Illuminate\Support\Enumerable', 'TValue');

        if (! (new ObjectType('Illuminate\Contracts\Support\Arrayable'))->isSuperTypeOf($valueType)->no()) {
            return null;
        }

        $node->name = new Identifier('all');

        return $node;
    }
}
