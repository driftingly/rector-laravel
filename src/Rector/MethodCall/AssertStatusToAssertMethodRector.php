<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\AssertStatusToAssertMethodTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AssertStatusToAssertMethodTest
 */
final class AssertStatusToAssertMethodRector extends AbstractRector
{
    /**
     * Status codes that have a dedicated assertion method on
     * Illuminate\Testing\Concerns\AssertsStatusCodes.
     *
     * @var array<int, non-empty-string>
     */
    private const STATUS_CODE_TO_METHOD = [
        200 => 'assertOk',
        201 => 'assertCreated',
        202 => 'assertAccepted',
        204 => 'assertNoContent',
        301 => 'assertMovedPermanently',
        302 => 'assertFound',
        304 => 'assertNotModified',
        307 => 'assertTemporaryRedirect',
        308 => 'assertPermanentRedirect',
        400 => 'assertBadRequest',
        401 => 'assertUnauthorized',
        402 => 'assertPaymentRequired',
        403 => 'assertForbidden',
        404 => 'assertNotFound',
        405 => 'assertMethodNotAllowed',
        406 => 'assertNotAcceptable',
        408 => 'assertRequestTimeout',
        409 => 'assertConflict',
        410 => 'assertGone',
        415 => 'assertUnsupportedMediaType',
        422 => 'assertUnprocessable',
        424 => 'assertFailedDependency',
        429 => 'assertTooManyRequests',
        500 => 'assertInternalServerError',
        503 => 'assertServiceUnavailable',
    ];

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
        $this->get('/')->assertStatus(201);
        $this->get('/')->assertStatus(202);
        $this->get('/')->assertStatus(204);
        $this->get('/')->assertStatus(301);
        $this->get('/')->assertStatus(302);
        $this->get('/')->assertStatus(304);
        $this->get('/')->assertStatus(307);
        $this->get('/')->assertStatus(308);
        $this->get('/')->assertStatus(400);
        $this->get('/')->assertStatus(401);
        $this->get('/')->assertStatus(402);
        $this->get('/')->assertStatus(403);
        $this->get('/')->assertStatus(404);
        $this->get('/')->assertStatus(405);
        $this->get('/')->assertStatus(406);
        $this->get('/')->assertStatus(408);
        $this->get('/')->assertStatus(409);
        $this->get('/')->assertStatus(410);
        $this->get('/')->assertStatus(415);
        $this->get('/')->assertStatus(422);
        $this->get('/')->assertStatus(424);
        $this->get('/')->assertStatus(429);
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
        $this->get('/')->assertCreated();
        $this->get('/')->assertAccepted();
        $this->get('/')->assertNoContent();
        $this->get('/')->assertMovedPermanently();
        $this->get('/')->assertFound();
        $this->get('/')->assertNotModified();
        $this->get('/')->assertTemporaryRedirect();
        $this->get('/')->assertPermanentRedirect();
        $this->get('/')->assertBadRequest();
        $this->get('/')->assertUnauthorized();
        $this->get('/')->assertPaymentRequired();
        $this->get('/')->assertForbidden();
        $this->get('/')->assertNotFound();
        $this->get('/')->assertMethodNotAllowed();
        $this->get('/')->assertNotAcceptable();
        $this->get('/')->assertRequestTimeout();
        $this->get('/')->assertConflict();
        $this->get('/')->assertGone();
        $this->get('/')->assertUnsupportedMediaType();
        $this->get('/')->assertUnprocessable();
        $this->get('/')->assertFailedDependency();
        $this->get('/')->assertTooManyRequests();
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

        if (! is_int($value)) {
            return null;
        }

        $replacementMethod = self::STATUS_CODE_TO_METHOD[$value] ?? null;

        if ($replacementMethod === null) {
            return null;
        }

        $methodCall->name = new Identifier($replacementMethod);
        $methodCall->args = [];

        return $methodCall;
    }
}
