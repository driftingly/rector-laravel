<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\RedirectBackToBackHelperRectorTest
 */
final class RedirectBackToBackHelperRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `redirect()->back()` and `Redirect::back()` with `back()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Redirect;

class MyController
{
    public function store()
    {
        return redirect()->back()->with('error', 'Incorrect Details.')
    }

    public function update()
    {
        return Redirect::back()->with('error', 'Incorrect Details.')
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Redirect;

class MyController
{
    public function store()
    {
        return back()->with('error', 'Incorrect Details.')
    }

    public function update()
    {
        return back()->with('error', 'Incorrect Details.')
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
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->updateRedirectHelperCall($node);
        }

        return $this->updateRedirectStaticCall($node);
    }

    private function updateRedirectHelperCall(MethodCall $methodCall): ?FuncCall
    {
        if (! $this->isName($methodCall->name, 'back')) {
            return null;
        }

        if (! $methodCall->var instanceof FuncCall) {
            return null;
        }

        if ($methodCall->var->getArgs() !== []) {
            return null;
        }

        if (! $this->isName($methodCall->var->name, 'redirect')) {
            return null;
        }

        $methodCall->var->name = new Name('back');
        $methodCall->var->args = $methodCall->getArgs();

        return $methodCall->var;
    }

    private function updateRedirectStaticCall(StaticCall $staticCall): ?FuncCall
    {
        if (! $this->isName($staticCall->class, 'Illuminate\Support\Facades\Redirect')) {
            return null;
        }

        if (! $this->isName($staticCall->name, 'back')) {
            return null;
        }

        return new FuncCall(new Name('back'), $staticCall->args);
    }
}
