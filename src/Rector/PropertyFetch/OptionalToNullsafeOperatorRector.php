<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\PropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/laravel/laravel/pull/5670
 * @see https://wiki.php.net/rfc/nullsafe_operator
 *
 * @see \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\OptionalToNullsafeOperatorRectorTest
 */
final class OptionalToNullsafeOperatorRector extends AbstractRector implements MinPhpVersionInterface, ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const EXCLUDE_METHODS = 'exclude_methods';

    private array $excludeMethods = [];

    public function __construct(
        private TypeChecker $typeChecker
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert simple calls to optional helper to use the nullsafe operator',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
optional($user)->getKey();
optional($user)->id;
// macro methods
optional($user)->present()->getKey();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$user?->getKey();
$user?->id;
// macro methods
optional($user)->present()->getKey();
CODE_SAMPLE
              ,
                    [
                        self::EXCLUDE_METHODS => ['present'],
                    ]
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class, MethodCall::class];
    }

    /**
     * @param MethodCall|PropertyFetch $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->var instanceof FuncCall) {
            return null;
        }

        if (! $this->isName($node->var->name, 'optional')) {
            return null;
        }

        if ($node instanceof MethodCall && $this->isNames($node->name, $this->excludeMethods)) {
            return null;
        }

        if (! isset($node->var->args[0])) {
            return null;
        }

        if (! $node->var->args[0] instanceof Arg) {
            return null;
        }

        if (isset($node->var->args[1]) && $node->var->args[1] instanceof Arg && ! $this->valueResolver->isNull(
            $node->var->args[1]->value
        )) {
            return null;
        }

        if ($this->typeChecker->isInstanceOf(
            $node->var->args[0]->value,
            [ConstFetch::class, Scalar::class, Array_::class]
        )) {
            return null;
        }

        if ($node instanceof PropertyFetch) {
            return new NullsafePropertyFetch($node->var->args[0]->value, $node->name);
        }

        return new NullsafeMethodCall($node->var->args[0]->value, $node->name);
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersion::PHP_80;
    }

    public function configure(array $configuration): void
    {
        $this->excludeMethods = $configuration[self::EXCLUDE_METHODS] ?? [];
    }
}
