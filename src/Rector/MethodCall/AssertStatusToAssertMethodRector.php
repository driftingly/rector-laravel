<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\LNumber;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
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
    public function testOk()
    {
        $this->get('/')->assertStatus(200);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_OK);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function testNoContent()
    {
        $this->get('/')->assertStatus(204);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function testUnauthorized()
    {
        $this->get('/')->assertStatus(401);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_UNAUTHORIZED);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
    }

    public function testForbidden()
    {
        $this->get('/')->assertStatus(403);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_FORBIDDEN);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }

    public function testNotFound()
    {
        $this->get('/')->assertStatus(404);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_NOT_FOUND);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
    }

    public function testMethodNotAllowed()
    {
        $this->get('/')->assertStatus(405);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_METHOD_NOT_ALLOWED);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testUnprocessableEntity()
    {
        $this->get('/')->assertStatus(422);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGone()
    {
        $this->get('/')->assertStatus(410);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_GONE);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_GONE);
    }

    public function testInternalServerError()
    {
        $this->get('/')->assertStatus(500);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testServiceUnavailable()
    {
        $this->get('/')->assertStatus(503);
        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE);
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testOk()
    {
        $this->get('/')->assertOk();
        $this->get('/')->assertOk();
        $this->get('/')->assertOk();
    }

    public function testNoContent()
    {
        $this->get('/')->assertNoContent();
        $this->get('/')->assertNoContent();
        $this->get('/')->assertNoContent();
    }

    public function testUnauthorized()
    {
        $this->get('/')->assertUnauthorized();
        $this->get('/')->assertUnauthorized();
        $this->get('/')->assertUnauthorized();
    }

    public function testForbidden()
    {
        $this->get('/')->assertForbidden();
        $this->get('/')->assertForbidden();
        $this->get('/')->assertForbidden();
    }

    public function testNotFound()
    {
        $this->get('/')->assertNotFound();
        $this->get('/')->assertNotFound();
        $this->get('/')->assertNotFound();
    }

    public function testMethodNotAllowed()
    {
        $this->get('/')->assertMethodNotAllowed();
        $this->get('/')->assertMethodNotAllowed();
        $this->get('/')->assertMethodNotAllowed();
    }

    public function testUnprocessableEntity()
    {
        $this->get('/')->assertUnprocessable();
        $this->get('/')->assertUnprocessable();
        $this->get('/')->assertUnprocessable();
    }

    public function testGone()
    {
        $this->get('/')->assertGone();
        $this->get('/')->assertGone();
        $this->get('/')->assertGone();
    }

    public function testInternalServerError()
    {
        $this->get('/')->assertInternalServerError();
        $this->get('/')->assertInternalServerError();
        $this->get('/')->assertInternalServerError();
    }

    public function testServiceUnavailable()
    {
        $this->get('/')->assertServiceUnavailable();
        $this->get('/')->assertServiceUnavailable();
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

        if (! $argValue instanceof LNumber && ! $argValue instanceof ClassConstFetch) {
            return null;
        }

        if ($argValue instanceof LNumber) {
            switch ($argValue->value) {
                case 200:
                    $replacementMethod = 'assertOk';
                    break;
                case 204:
                    $replacementMethod = 'assertNoContent';
                    break;
                case 401:
                    $replacementMethod = 'assertUnauthorized';
                    break;
                case 403:
                    $replacementMethod = 'assertForbidden';
                    break;
                case 404:
                    $replacementMethod = 'assertNotFound';
                    break;
                case 405:
                    $replacementMethod = 'assertMethodNotAllowed';
                    break;
                case 410:
                    $replacementMethod = 'assertGone';
                    break;
                case 422:
                    $replacementMethod = 'assertUnprocessable';
                    break;
                case 500:
                    $replacementMethod = 'assertInternalServerError';
                    break;
                case 503:
                    $replacementMethod = 'assertServiceUnavailable';
                    break;
                default:
                    $replacementMethod = null;
                    break;
            }
        } else {
            if (! in_array($this->getName($argValue->class), [
                'Illuminate\Http\Response',
                'Symfony\Component\HttpFoundation\Response',
            ], true)) {
                return null;
            }

            switch ($this->getName($argValue->name)) {
                case 'HTTP_OK':
                    $replacementMethod = 'assertOk';
                    break;
                case 'HTTP_NO_CONTENT':
                    $replacementMethod = 'assertNoContent';
                    break;
                case 'HTTP_UNAUTHORIZED':
                    $replacementMethod = 'assertUnauthorized';
                    break;
                case 'HTTP_FORBIDDEN':
                    $replacementMethod = 'assertForbidden';
                    break;
                case 'HTTP_NOT_FOUND':
                    $replacementMethod = 'assertNotFound';
                    break;
                case 'HTTP_METHOD_NOT_ALLOWED':
                    $replacementMethod = 'assertMethodNotAllowed';
                    break;
                case 'HTTP_GONE':
                    $replacementMethod = 'assertGone';
                    break;
                case 'HTTP_UNPROCESSABLE_ENTITY':
                    $replacementMethod = 'assertUnprocessable';
                    break;
                case 'HTTP_INTERNAL_SERVER_ERROR':
                    $replacementMethod = 'assertInternalServerError';
                    break;
                case 'HTTP_SERVICE_UNAVAILABLE':
                    $replacementMethod = 'assertServiceUnavailable';
                    break;
                default:
                    $replacementMethod = null;
                    break;
            }
        }

        if ($replacementMethod === null) {
            return null;
        }

        $methodCall->name = new Identifier($replacementMethod);
        $methodCall->args = [];

        return $methodCall;
    }
}
