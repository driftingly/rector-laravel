<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
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
    public const string USING_POSTGRES_DRIVER = 'usingPostgresDriver';

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
                    [WhereToWhereLikeRector::USING_POSTGRES_DRIVER => false]
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
                    [WhereToWhereLikeRector::USING_POSTGRES_DRIVER => true]
                ),
            ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof StaticCall) {
            return null;
        }

        return $this->refactorMethodCall($node);
    }

    private function refactorMethodCall(MethodCall $node): ?MethodCall
    {
        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Database\Query\Builder'))) {
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

        Assert::keyExists($configuration, self::USING_POSTGRES_DRIVER);
        Assert::boolean($configuration[self::USING_POSTGRES_DRIVER]);
        $this->usingPostgresDriver = $configuration[self::USING_POSTGRES_DRIVER];
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
        if ($methodCall->args[2] instanceof Arg && $methodCall->args[2]->name instanceof Identifier) {
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
