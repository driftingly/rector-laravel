<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Transform\ValueObject\StringToClassConstant;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\EventStringToClassConstantRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see EventStringToClassConstantRectorTest
 */
final class EventStringToClassConstantRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * Dispatcher methods that take the event name as their first argument.
     *
     * @var string[]
     */
    private const array EVENT_NAME_METHODS = [
        'dispatch',
        'fire',
        'flush',
        'forget',
        'hasListeners',
        'listen',
        'push',
        'until',
    ];

    /**
     * @var StringToClassConstant[]
     */
    private array $stringToClassConstants = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Turns a string event name into a class constant, but only where the event dispatcher is used',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Event::listen('auth.login', function () {});
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function () {});
CODE_SAMPLE,
                [new StringToClassConstant('auth.login', 'Illuminate\Auth\Events\Login', 'class')]
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, MethodCall::class, FuncCall::class];
    }

    public function configure(array $configuration): void
    {
        Assert::allIsInstanceOf($configuration, StringToClassConstant::class);

        $this->stringToClassConstants = $configuration;
    }

    /**
     * @param  StaticCall|MethodCall|FuncCall  $node
     */
    public function refactor(Node $node): StaticCall|MethodCall|FuncCall|null
    {
        if (! $this->isEventDispatchingCall($node)) {
            return null;
        }

        $firstArg = $node->args[0] ?? null;

        if (! $firstArg instanceof Arg) {
            return null;
        }

        if ($firstArg->value instanceof Array_) {
            return $this->refactorArray($node, $firstArg->value);
        }

        $classConstFetch = $this->matchClassConstFetch($firstArg->value);

        if (! $classConstFetch instanceof ClassConstFetch) {
            return null;
        }

        $firstArg->value = $classConstFetch;

        return $node;
    }

    private function refactorArray(StaticCall|MethodCall|FuncCall $node, Array_ $array): StaticCall|MethodCall|FuncCall|null
    {
        $hasChanged = false;

        foreach ($array->items as $arrayItem) {
            if ($arrayItem->key !== null) {
                continue;
            }

            $classConstFetch = $this->matchClassConstFetch($arrayItem->value);

            if (! $classConstFetch instanceof ClassConstFetch) {
                continue;
            }

            $arrayItem->value = $classConstFetch;
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function matchClassConstFetch(Node $node): ?ClassConstFetch
    {
        if (! $node instanceof String_) {
            return null;
        }

        foreach ($this->stringToClassConstants as $stringToClassConstant) {
            if ($node->value !== $stringToClassConstant->getString()) {
                continue;
            }

            return new ClassConstFetch(
                new FullyQualified($stringToClassConstant->getClass()),
                $stringToClassConstant->getConstant()
            );
        }

        return null;
    }

    private function isEventDispatchingCall(StaticCall|MethodCall|FuncCall $node): bool
    {
        if ($node instanceof FuncCall) {
            return $node->name instanceof Name && $this->isName($node->name, 'event');
        }

        if (! $node->name instanceof Identifier) {
            return false;
        }

        if (! $this->isNames($node->name, self::EVENT_NAME_METHODS)) {
            return false;
        }

        [$callerNode, $className] = match (true) {
            $node instanceof StaticCall => [$node->class, 'Illuminate\Support\Facades\Event'],
            $node instanceof MethodCall => [$node->var, 'Illuminate\Contracts\Events\Dispatcher'],
        };

        return $this->isObjectType($callerNode, new ObjectType($className));
    }
}
