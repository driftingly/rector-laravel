<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\AssertStatusToAssertMethodTest
 */
final class AssertStatusToAssertMethodRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `(new \Illuminate\Testing\TestResponse)->assertStatus(200)` with `(new \Illuminate\Testing\TestResponse)->assertOk()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testFoo()
    {
        $this->get('/')->assertStatus(200);
        $this->get('/')->assertStatus(204);
        $this->get('/')->assertStatus(401);
        $this->get('/')->assertStatus(403);
        $this->get('/')->assertStatus(404);
        $this->get('/')->assertStatus(405);
        $this->get('/')->assertStatus(422);
        $this->get('/')->assertStatus(410);
        $this->get('/')->assertStatus(500);
        $this->get('/')->assertStatus(503);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testFoo()
    {
        $this->get('/')->assertOk();
        $this->get('/')->assertNoContent();
        $this->get('/')->assertUnauthorized();
        $this->get('/')->assertForbidden();
        $this->get('/')->assertNotFound();
        $this->get('/')->assertMethodNotAllowed();
        $this->get('/')->assertUnprocessable();
        $this->get('/')->assertGone();
        $this->get('/')->assertInternalServerError();
        $this->get('/')->assertServiceUnavailable();
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        return $this->updateAssertStatusCall($node);
    }

    private function updateAssertStatusCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isName($methodCall->name, 'assertStatus')) {
            return null;
        }

        if (! $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Testing\TestResponse'))) {
            return null;
        }

        if (count($methodCall->getArgs()) !== 1) {
            return null;
        }

        $arg = $methodCall->getArgs()[0];
        $argValue = $arg->value;

        // we can check if the arg is an integer even if it comes from a constant
        $type = $this->getType($argValue);

        if (! $type->isInteger()->yes()) {
            return null;
        }

        // we want the value of the integer if it's known
        $value = ($type->getConstantScalarValues()[0] ?? null);

        if ($value === null) {
            return null;
        }

        $replacementMethod = match ($value) {
            200 => 'assertOk',
            204 => 'assertNoContent',
            401 => 'assertUnauthorized',
            403 => 'assertForbidden',
            404 => 'assertNotFound',
            405 => 'assertMethodNotAllowed',
            410 => 'assertGone',
            422 => 'assertUnprocessable',
            500 => 'assertInternalServerError',
            503 => 'assertServiceUnavailable',
            default => null
        };

        if ($replacementMethod === null) {
            return null;
        }

        $methodCall->name = new Identifier($replacementMethod);
        $methodCall->args = [];

        return $methodCall;
    }
}
