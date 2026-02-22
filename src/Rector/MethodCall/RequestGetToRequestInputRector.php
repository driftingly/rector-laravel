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
 * @see \RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\RequestGetToRequestInputRectorTest
 */
final class RequestGetToRequestInputRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `Illuminate\Http\Request::get()` calls with `Illuminate\Http\Request::input()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $name = $request->get('name', 'default');
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Http\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $name = $request->input('name', 'default');
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
        if ($node->isFirstClassCallable()) {
            return null;
        }

        if (! $this->isName($node->name, 'get')) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Http\Request'))) {
            return null;
        }

        $node->name = new Identifier('input');

        return $node;
    }
}
