<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\PropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Rector\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @changelog https://github.com/laravel/laravel/pull/5670
 * @changelog https://github.com/laravel/framework/pull/38868
 * @changelog https://wiki.php.net/rfc/nullsafe_operator
 *
 * @see \RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\OptionalToNullsafeOperatorRectorTest
 */
final class OptionalToNullsafeOperatorRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @var string
     */
    public const EXCLUDE_METHODS = 'exclude_methods';

    /**
     * @var array<class-string<Expr>>
     */
    private const SKIP_VALUE_TYPES = [ConstFetch::class, Scalar::class, Array_::class, ClassConstFetch::class];

    /**
     * @var string[]
     */
    private $excludeMethods = [];

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
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
     * @param  MethodCall|PropertyFetch  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->var instanceof FuncCall) {
            return null;
        }

        if (! $this->isName($node->var->name, 'optional')) {
            return null;
        }

        // exclude macro methods
        if ($node instanceof MethodCall && $this->isNames($node->name, $this->excludeMethods)) {
            return null;
        }

        if (! isset($node->var->args[0])) {
            return null;
        }

        // skip if the second arg exists and not null
        if ($this->hasCallback($node->var)) {
            return null;
        }

        /** @var Arg $firstArg */
        $firstArg = $node->var->args[0];

        // skip if the first arg cannot be used as variable directly
        if ($this->shouldSkipFirstArg($firstArg->value)) {
            return null;
        }

        if ($node instanceof PropertyFetch) {
            return new NullsafePropertyFetch($firstArg->value, $node->name);
        }

        return new NullsafeMethodCall($firstArg->value, $node->name, $node->args);
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersion::PHP_80;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $excludeMethods = $configuration[self::EXCLUDE_METHODS] ?? $configuration;
        Assert::isArray($excludeMethods);
        Assert::allString($excludeMethods);

        $this->excludeMethods = $excludeMethods;
    }

    private function hasCallback(FuncCall $funcCall): bool
    {
        return isset($funcCall->args[1]) && $funcCall->args[1] instanceof Arg && ! $this->valueResolver->isNull(
            $funcCall->args[1]->value
        );
    }

    private function shouldSkipFirstArg(Expr $expr): bool
    {
        foreach (self::SKIP_VALUE_TYPES as $type) {
            if ($expr instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
