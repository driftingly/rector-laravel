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
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see https://github.com/laravel/framework/pull/52147
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
            'Changes `where` method calls to `whereLike` method calls in the Eloquent & Query Builder', [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$query->where('name', 'like', 'Rector');
$query->orWhere('name', 'like', 'Rector');
$query->where('name', 'like binary', 'Rector');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$query->whereLike('name', 'Rector');
$query->orWhereLike('name', 'Rector');
$query->whereLike('name', 'Rector', true);
CODE_SAMPLE
                    ,
                    ['usingPostgresDriver' => false]
                ),
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$query->where('name', 'ilike', 'Rector');
$query->orWhere('name', 'ilike', 'Rector');
$query->where('name', 'like', 'Rector');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$query->whereLike('name', 'Rector');
$query->orWhereLike('name', 'Rector');
$query->whereLike('name', 'Rector', true);
CODE_SAMPLE
                    ,
                    ['usingPostgresDriver' => true]
                ),
            ]);
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

        if (! in_array($this->getLowercaseMethodCallName($node), array_keys(self::WHERE_LIKE_METHODS), true)) {
            return null;
        }

        if (count($node->getArgs()) !== 3) {
            return null;
        }

        $likeParameter = $this->getLikeParameterUsedInQuery($node);

        if (! in_array($likeParameter, ['like', 'like binary', 'ilike', 'not like', 'not like binary', 'not ilike'], true)) {
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

    private function getLikeParameterUsedInQuery(MethodCall $methodCall): ?string
    {
        if (! $methodCall->args[1] instanceof Arg) {
            return null;
        }

        if (! $methodCall->args[1]->value instanceof String_) {
            return null;
        }

        return strtolower($methodCall->args[1]->value->value);
    }

    private function setNewNodeName(MethodCall $methodCall, string $likeParameter): void
    {
        $newNodeName = self::WHERE_LIKE_METHODS[$this->getLowercaseMethodCallName($methodCall)];

        if (str_contains($likeParameter, 'not')) {
            $newNodeName = str_replace('Like', 'NotLike', $newNodeName);
        }

        $methodCall->name = new Identifier($newNodeName);
    }

    private function setCaseSensitivity(MethodCall $methodCall, string $likeParameter): void
    {
        // Case sensitive query in MySQL
        if (in_array($likeParameter, ['like binary', 'not like binary'], true)) {
            $methodCall->args[] = $this->getCaseSensitivityArgument($methodCall);
        }

        // Case sensitive query in Postgres
        if ($this->usingPostgresDriver && in_array($likeParameter, ['like', 'not like'], true)) {
            $methodCall->args[] = $this->getCaseSensitivityArgument($methodCall);
        }
    }

    private function getCaseSensitivityArgument(MethodCall $methodCall): Arg
    {
        if ($methodCall->args[2] instanceof Arg && $methodCall->args[2]->name !== null) {
            return new Arg(
                new ConstFetch(new Name('true')),
                name: new Identifier('caseSensitive')
            );
        }

        return new Arg(new ConstFetch(new Name('true')));
    }

    private function getLowercaseMethodCallName(MethodCall $methodCall): string
    {
        return strtolower((string) $this->getName($methodCall->name));
    }
}
