<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\RequestGetToRequestInputRectorTest
 */
final class RequestGetToRequestInputRector extends AbstractRector
{
    private const string REQUEST_FACADE = 'Illuminate\Support\Facades\Request';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `Illuminate\Http\Request::get()` calls with `Illuminate\Http\Request::input()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $name = $request->get('name', 'default');
        Request::get('name');
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $name = $request->input('name', 'default');
        Request::input('name');
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
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): StaticCall|MethodCall|null
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        if (! $this->isName($node->name, 'get')) {
            return null;
        }

        if ($node instanceof StaticCall) {
            if (! $this->isName($node->class, self::REQUEST_FACADE)) {
                return null;
            }

            $node->name = new Identifier('input');

            return $node;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Http\Request'))) {
            return null;
        }

        $node->name = new Identifier('input');

        return $node;
    }
}
