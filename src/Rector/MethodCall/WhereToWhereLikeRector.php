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
    /**
     * @var string
     */
    public const USING_POSTGRES_DRIVER = 'usingPostgresDriver';

    /**
     * @var mixed[]
     */
    private const WHERE_LIKE_METHODS = [
        'where' => 'whereLike',
        'orwhere' => 'orWhereLike',
    ];

    /**
     * @var bool
     */
    private $usingPostgresDriver = false;

    public function getRuleDefinition(): RuleDefinition
    {
        $description = "Changes `where` method and static calls to `whereLike` calls in the Eloquent & Query Builder.\n\n"
            . 'Can be configured for the Postgres driver with `[WhereToWhereLikeRector::USING_POSTGRES_DRIVER => true]`.';

        return new RuleDefinition(
            $description, [
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
        if ($node instanceof StaticCall &&
            ! $this->isObjectType($node->class, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        if ($node instanceof MethodCall &&
            ! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Database\Query\Builder'))) {
            return null;
        }

        if (! in_array($this->getLowercaseCallName($node), array_keys(self::WHERE_LIKE_METHODS), true)) {
            return null;
        }

        if (count($node->getArgs()) !== 3) {
            return null;
        }

        // Expressions are not supported with the `like` operator
        if ($node->args[2] instanceof Arg &&
            $this->isObjectType($node->args[2]->value, new ObjectType('Illuminate\Contracts\Database\Query\Expression'))
        ) {
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

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     */
    private function getLikeParameterUsedInQuery($call): ?string
    {
        if (! $call->args[1] instanceof Arg) {
            return null;
        }

        if (! $call->args[1]->value instanceof String_) {
            return null;
        }

        return strtolower($call->args[1]->value->value);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     */
    private function setNewNodeName($call, string $likeParameter): void
    {
        $newNodeName = self::WHERE_LIKE_METHODS[$this->getLowercaseCallName($call)];

        if (strpos($likeParameter, 'not') !== false) {
            $newNodeName = str_replace('Like', 'NotLike', $newNodeName);
        }

        $call->name = new Identifier($newNodeName);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     */
    private function setCaseSensitivity($call, string $likeParameter): void
    {
        // Case sensitive query in MySQL
        if (in_array($likeParameter, ['like binary', 'not like binary'], true)) {
            $call->args[] = $this->getCaseSensitivityArgument($call);
        }

        // Case sensitive query in Postgres
        if ($this->usingPostgresDriver && in_array($likeParameter, ['like', 'not like'], true)) {
            $call->args[] = $this->getCaseSensitivityArgument($call);
        }
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     */
    private function getCaseSensitivityArgument($call): Arg
    {
        if ($call->args[2] instanceof Arg && $call->args[2]->name instanceof Identifier) {
            return new Arg(
                new ConstFetch(new Name('true')),
                false,
                false,
                [],
                new Identifier('caseSensitive')
            );
        }

        return new Arg(new ConstFetch(new Name('true')));
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     */
    private function getLowercaseCallName($call): string
    {
        return strtolower((string) $this->getName($call->name));
    }
}
