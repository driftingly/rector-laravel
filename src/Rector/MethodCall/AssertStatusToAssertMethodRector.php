<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\RedirectBackToBackHelperRectorTest
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
    }

    public function testNoContent()
    {
        $this->get('/')->assertStatus(204);
    }

    public function testForbidden()
    {
        $this->get('/')->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->get('/')->assertStatus(404);
    }

    public function testUnauthorized()
    {
        $this->get('/')->assertStatus(401);
    }

    public function testUnprocessableEntity()
    {
        $this->get('/')->assertStatus(422);
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
    }

    public function testNoContent()
    {
        $this->get('/')->assertNoContent();
    }

    public function testForbidden()
    {
        $this->get('/')->assertForbidden();
    }

    public function testNotFound()
    {
        $this->get('/')->assertNotFound();
    }

    public function testUnauthorized()
    {
        $this->get('/')->assertUnauthorized();
    }

    public function testUnprocessableEntity()
    {
        $this->get('/')->assertUnprocessable();
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
     * @param MethodCall $node
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

        if (count($methodCall->getArgs()) <> 1) {
            return null;
        }

        $arg = $methodCall->args[0];
        $argValue = $arg->value;

        if (! $argValue instanceof Node\Scalar\LNumber && ! $argValue instanceof Node\Expr\ClassConstFetch) {
            return null;
        }

        if ($argValue instanceof Node\Scalar\LNumber) {
            $replacementMethod = match ($argValue->value) {
                200 => 'assertOk',
                204 => 'assertNoContent',
                401 => 'assertUnauthorized',
                403 => 'assertForbidden',
                404 => 'assertNotFound',
                422 => 'assertUnprocessable',
                default => null
            };
        } elseif ($argValue instanceof Node\Expr\ClassConstFetch) {
            if (! in_array($argValue->class->toString(), [
                'Illuminate\Http\Response',
                'Symfony\Component\HttpFoundation\Response'
            ])) {
               return null;
            }

            $replacementMethod = match ($argValue->name->name) {
                'HTTP_OK' => 'assertOk',
                'HTTP_NO_CONTENT' => 'assertNoContent',
                'HTTP_UNAUTHORIZED' => 'assertUnauthorized',
                'HTTP_FORBIDDEN' => 'assertForbidden',
                'HTTP_NOT_FOUND' => 'assertNotFound',
                'HTTP_UNPROCESSABLE_ENTITY' => 'assertUnprocessable',
                default => null
            };
        }

        if ($replacementMethod === null) {
            return null;
        }

        $methodCall->name = new Node\Identifier($replacementMethod);
        $methodCall->args = [];

        return $methodCall;
    }
}
