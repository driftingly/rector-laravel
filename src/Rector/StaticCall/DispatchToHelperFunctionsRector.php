<?php

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\DispatchToHelperFunctionsRectorTest
 */
final class DispatchToHelperFunctionsRector extends AbstractRector
{
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }
    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use the event or dispatch helpers instead of the static dispatch method.', [
            new CodeSample(
                'ExampleEvent::dispatch($email);',
                'event(new ExampleEvent($email));'
            ),
            new CodeSample(
                'ExampleJob::dispatch($email);',
                'dispatch(new ExampleJob($email));'
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof StaticCall) {
            return null;
        }

        if (! $this->isName($node->name, 'dispatch')) {
            return null;
        }

        $class = $node->class;

        if (! $class instanceof Name) {
            return null;
        }

        $classReflection = $this->getClassReflection($node);
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if ($this->usesBusDispatchable($classReflection)) {
            return $this->createDispatchableCall($node, 'dispatch');
        }

        if ($this->usesEventDispatchable($classReflection)) {
            return $this->createDispatchableCall($node, 'event');
        }

        return null;
    }

    private function getClassReflection(StaticCall $staticCall): ?ClassReflection
    {
        $type = $this->getType($staticCall->class);
        if (! $type instanceof ObjectType) {
            return null;
        }

        try {
            return $this->reflectionProvider->getClass(
                $type->getClassName()
            );
        } catch (ClassNotFoundException $exception) {
        }

        return null;
    }

    private function usesBusDispatchable(ClassReflection $classReflection): bool
    {
        $traits = array_keys($classReflection->getTraits(true));

        return in_array('Illuminate\Foundation\Bus\Dispatchable', $traits, true);
    }

    private function usesEventDispatchable(ClassReflection $classReflection): bool
    {
        $traits = array_keys($classReflection->getTraits(true));

        return in_array('Illuminate\Foundation\Events\Dispatchable', $traits, true);
    }

    private function createDispatchableCall(StaticCall $staticCall, string $method): ?FuncCall
    {
        $class = $staticCall->class;
        if (! $class instanceof Name) {
            return null;
        }

        return new FuncCall(new Name($method), [
            new Arg(new New_(new FullyQualified($class), $staticCall->args)),
        ]);
    }
}
