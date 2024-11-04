<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use ReflectionException;
use ReflectionMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\EloquentMagicMethodToQueryBuilderRectorTest
 */
final class EloquentMagicMethodToQueryBuilderRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const EXCLUDE_METHODS = 'exclude_methods';

    /**
     * @var string[]
     */
    private $excludeMethods = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'The EloquentMagicMethodToQueryBuilderRule is designed to automatically transform certain magic method calls on Eloquent Models into corresponding Query Builder method calls.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::find(1);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use App\Models\User;

$user = User::query()->find(1);
CODE_SAMPLE
                    , [
                        self::EXCLUDE_METHODS => ['find'],
                    ]),

            ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $resolvedType = $this->nodeTypeResolver->getType($node->class);

        // like for variables, example "$namespace"
        // @phpstan-ignore-next-line
        if (! method_exists($resolvedType, 'getClassName')) {
            return null;
        }

        $className = (string) $resolvedType->getClassName();
        $originalClassName = $this->getName($node->class); // like "self" or "App\Models\User"

        if ($originalClassName === null) {
            return null;
        }

        // does not extend Eloquent Model
        if (! is_subclass_of($className, Model::class)) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = $node->name->toString();

        // if not a magic method
        if (! $this->isMagicMethod($className, $methodName)) {
            return null;
        }

        // if method belongs to Eloquent Query Builder or Query Builder
        if (! $this->isPublicMethod(EloquentQueryBuilder::class, $methodName) && ! $this->isPublicMethod(
            QueryBuilder::class,
            $methodName
        )) {
            return null;
        }

        $staticCall = $this->nodeFactory->createStaticCall($originalClassName, 'query');

        $methodCall = $this->nodeFactory->createMethodCall($staticCall, $methodName);
        foreach ($node->args as $arg) {
            $methodCall->args[] = $arg;
        }

        return $methodCall;
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

    public function isMagicMethod(string $className, string $methodName): bool
    {
        if (in_array($methodName, $this->excludeMethods, true)) {
            return false;
        }

        try {
            $reflectionMethod = new ReflectionMethod($className, $methodName);
        } catch (ReflectionException $exception) {
            return true; // method does not exist => is magic method
        }

        return false; // not a magic method
    }

    public function isPublicMethod(string $className, string $methodName): bool
    {
        try {
            $reflectionMethod = new ReflectionMethod($className, $methodName);

            // if not public
            if (! $reflectionMethod->isPublic()) {
                return false;
            }

            // if static
            if ($reflectionMethod->isStatic()) {
                return false;
            }
        } catch (ReflectionException $exception) {
            return false; // method does not exist => is magic method
        }

        return true; // method exist
    }
}
