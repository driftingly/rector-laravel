<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
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

        $arg = $methodCall->getArgs()[0];
        $argValue = $arg->value;

        if (! $argValue instanceof String_) {
            return null;
        }

        $supportedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

        if (in_array($argValue->value, $supportedMethods, true)) {
            $methodCall->name = new Identifier(strtolower($argValue->value) . 'Json');
            $methodCall->args = array_slice($methodCall->args, 1);

            return $methodCall;
        }

        return null;
    }
}
