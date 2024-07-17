<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\JsonCallToExplicitJsonCallRectorTest
 */
final class JsonCallToExplicitJsonCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change method calls from $this->json to $this->postJson, $this->putJson, etc.',
            [
                new CodeSample(
                    // code before
                    '$this->json("POST", "/api/v1/users", $data);',
                    // code after
                    '$this->postJson("/api/v1/users", $data);'
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->updateCall($node);
        }

        return null;
    }

    private function updateCall(MethodCall $methodCall): ?MethodCall
    {
        $methodCallName = $this->getName($methodCall->name);
        if ($methodCallName === null) {
            return null;
        }

        if (! $this->isObjectType(
            $methodCall->var,
            new ObjectType('Illuminate\Foundation\Testing\Concerns\MakesHttpRequests')
        )) {
            return null;
        }

        if ($methodCallName !== 'json') {
            return null;
        }

        $methodCallArgs = $methodCall->getArgs();

        if (count($methodCallArgs) < 2) {
            return null;
        }

        $firstArg = $methodCallArgs[0]->value;

        if (! $firstArg instanceof String_) {
            return null;
        }

        $lowercaseMethodName = strtolower($firstArg->value);

        $supportedMethods = ['get', 'post', 'put', 'patch', 'delete', 'options'];

        if (! in_array($lowercaseMethodName, $supportedMethods, true)) {
            return null;
        }

        if ($lowercaseMethodName === 'get' && count($methodCallArgs) > 2) {
            return $this->refactorGetMethodCall($methodCall);
        }

        $methodCall->name = $this->getMethodCallName($lowercaseMethodName);
        $methodCall->args = array_slice($methodCall->args, 1);

        return $methodCall;
    }

    /**
     * Set the $data argument from the json('GET') call (3rd argument)
     * as the route() helper second argument
     */
    private function refactorGetMethodCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isUsingChangeableRouteHelper($methodCall)) {
            return null;
        }

        $thirdArg = $methodCall->getArgs()[2];

        // If it's a named argument, and it's not $data, we won't refactor
        if ($thirdArg->name !== null && ! $this->isName($thirdArg, 'data')) {
            return null;
        }

        /** @var FuncCall $routeHelperCall */
        $routeHelperCall = $methodCall->getArgs()[1]->value;

        $routeHelperCall->args = [
            $routeHelperCall->args[0],
            new Arg($thirdArg->value),
        ];

        $methodCall->name = $this->getMethodCallName('get');
        $methodCall->args = [new Arg($routeHelperCall)];

        return $methodCall;
    }

    private function isUsingChangeableRouteHelper(MethodCall $methodCall): bool
    {
        $methodCallArgs = $methodCall->getArgs();

        // More than 3 arguments means we loose $headers or $options if we refactor
        if (count($methodCallArgs) !== 3) {
            return false;
        }

        $secondArg = $methodCallArgs[1]->value;

        if (! ($secondArg instanceof FuncCall && $this->isName($secondArg, 'route'))) {
            return false;
        }

        // If there is more than 1 argument in the route() helper
        // we have to take into account merging the $data argument,
        // but it's too unpredictable to refactor
        return count($secondArg->args) === 1;
    }

    private function getMethodCallName(string $lowercaseMethodName): Identifier
    {
        return new Identifier("{$lowercaseMethodName}Json");
    }
}
