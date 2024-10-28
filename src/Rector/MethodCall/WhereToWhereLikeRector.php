<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see https://github.com/laravel/framework/pull/52147
 *
 * @see \RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\WhereToWhereLikeRectorTest
 * @see \RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\WhereToWhereLikeRectorPostgresTest
 */
final class WhereToWhereLikeRector extends AbstractRector implements ConfigurableRectorInterface
{
    private const array WHERE_LIKE_METHODS = [
        'where' => 'whereLike',
        'orwhere' => 'orWhereLike',
    ];

    private bool $usingPostgresDriver = false;

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

        if (!in_array($likeParameter, ['like', 'like binary', 'ilike', 'not like', 'not like binary', 'not ilike'])) {
            return null;
        }

        $this->setNewNodeName($node, $likeParameter);

        $this->setCaseSensitivity($node, $likeParameter);

        // Remove the second argument (the 'like' operator)
        unset($node->args[1]);

        return $node;
    }

    public function configure(array $configuration): void
    {
        if ($configuration === []) {
            $this->usingPostgresDriver = false;
            return;
        }

        Assert::count($configuration, 1);
        Assert::keyExists($configuration, 'usingPostgresDriver');
        Assert::boolean($configuration['usingPostgresDriver']);
        $this->usingPostgresDriver = $configuration['usingPostgresDriver'];
    }

    private function getLikeParameterUsedInQuery(MethodCall $node): ?string
    {
        if (! $node->args[1]->value instanceof String_) {
            return null;
        }

        return strtolower($node->args[1]->value->value);
    }

    private function setNewNodeName(MethodCall $node, string $likeParameter): void
    {
        $newNodeName = self::WHERE_LIKE_METHODS[$node->name->toLowerString()];

        if (str_contains($likeParameter, 'not')) {
            $newNodeName = str_replace('Like', 'NotLike', $newNodeName);
        }

        $node->name = new Identifier($newNodeName);
    }

    private function setCaseSensitivity(MethodCall $node, string $likeParameter): void
    {
        // Case sensitive query in MySQL
        if (in_array($likeParameter, ['like binary', 'not like binary'])) {
            $node->args[] = $this->getCaseSensitivityArgument($node);
        }

        // Case sensitive query in Postgres
        if ($this->usingPostgresDriver && in_array($likeParameter, ['like', 'not like'])) {
            $node->args[] = $this->getCaseSensitivityArgument($node);
        }
    }

    private function getCaseSensitivityArgument(MethodCall $node): Arg
    {
        if ($node->args[2]->name !== null) {
            return new Arg(
                new ConstFetch(new Name('true')),
                name: new Identifier('caseSensitive')
            );
        }

        return new Arg(new ConstFetch(new Name('true')));
    }


}
