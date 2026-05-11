<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\This_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\PHPStan\ScopeFetcher;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\CarbonSetTestNowToTravelToRectorTest;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CarbonSetTestNowToTravelToRectorTest
 */
final class CarbonSetTestNowToTravelToRector extends AbstractRector
{
    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Use the `$this->travelTo()` method in Laravel\'s `TestCase` class instead of the `Carbon::setTestNow()` method.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\TestCase;

class SomeTest extends TestCase
{
    public function test()
    {
        Carbon::setTestNow('2024-08-11');
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\TestCase;

class SomeTest extends TestCase
{
    public function test()
    {
        $this->travelTo('2024-08-11');
    }
}
CODE_SAMPLE
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?MethodCall
    {
        if (! $node instanceof StaticCall) {
            return null;
        }

        $scope = ScopeFetcher::fetch($node);

        if (! $this->isInLaravelTestCaseScope($scope)) {
            return null;
        }

        if (! $this->isName($node->name, 'setTestNow')) {
            return null;
        }

        if (! $this->isCarbon($node->class)) {
            return null;
        }

        $args = $node->args === []
            ? [new Arg($this->nodeFactory->createNull())]
            : $node->args;

        return $this->nodeFactory->createMethodCall(
            new Variable('this'),
            'travelTo',
            $args,
        );
    }

    private function isInLaravelTestCaseScope(Scope $scope): bool
    {
        $testCaseClass = 'Illuminate\Foundation\Testing\TestCase';

        if ($scope->isInClass()) {
            return $scope->getClassReflection()->isSubclassOfClass($this->reflectionProvider->getClass($testCaseClass));
        }

        // Pest / other closures: `@param-closure-this` on the test runner makes `$this` a TestCase.
        $thisType = $scope->getType(new This_());
        $laravelTestCaseType = new ObjectType($testCaseClass);

        return $laravelTestCaseType->isSuperTypeOf($thisType)->yes();
    }

    private function isCarbon(Node $node): bool
    {
        return $this->isObjectType($node, new ObjectType('Carbon\Carbon')) ||
            $this->isObjectType($node, new ObjectType('Carbon\CarbonImmutable')) ||
            $this->isObjectType($node, new ObjectType('Illuminate\Support\Carbon'));
    }
}
