<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\WhereToWhereLikeRectorTest
 */
final class WhereToWhereLikeRector extends AbstractRector
{
    private const array WHERE_LIKE_METHODS = [
        'where' => 'whereLike',
        'orwhere' => 'orWhereLike',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes `where` method call to `whereLike` method call in Laravel Query Builder',
            [
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
        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Query\Builder'))) {
            return null;
        }

        if (!in_array(strtolower((string) $this->getName($node->name)), array_keys(self::WHERE_LIKE_METHODS))) {
            return null;
        }

        if (count($node->getArgs()) !== 3) {
            return null;
        }

        $likeParameter = $this->getLikeParameterUsedInQuery($node);

        if (!in_array($likeParameter, ['like', 'like binary', 'not like', 'not like binary'])) {
            return null;
        }

        $newNodeName = self::WHERE_LIKE_METHODS[$node->name->toLowerString()];

        if (str_contains($likeParameter, 'not')) {
            $newNodeName = str_replace('Like', 'NotLike', $newNodeName);
        }

        $node->name = new Node\Identifier($newNodeName);
        unset($node->args[1]);

        if (in_array($likeParameter, ['like binary', 'not like binary'])) {
            $node->args[] = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('true')));
        }

        return $node;
    }

    private function getLikeParameterUsedInQuery(MethodCall $node): ?string
    {
        if (! $node->args[1]->value instanceof String_) {
            return null;
        }

        return strtolower($node->args[1]->value->value);
    }
}
