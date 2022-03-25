<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Rector\Defluent\NodeAnalyzer\FluentChainMethodCallNodeAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\RedirectBackToBackHelperRectorTest
 */

final class RedirectBackToBackHelperRector extends AbstractRector
{
    public function __construct(
        private readonly FluentChainMethodCallNodeAnalyzer $fluentChainMethodCallNodeAnalyzer,
    ) {
    }

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
     * @param $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            if (! $this->isName($node->name, 'back')) {
                return null;
            }

            $rootExpr = $this->fluentChainMethodCallNodeAnalyzer->resolveRootExpr($node);
            $parentNode = $rootExpr->getAttribute(AttributeKey::PARENT_NODE);

            if ($this->isNotRedirectBackMethodCall($parentNode)) {
                return null;
            }

            $this->removeNode($node);

            $parentNode->var->name = new Name('back');

            return $parentNode;
        }

        if ($this->isNotRedirectBackStaticCall($node)) {
            return null;
        }

        return new FuncCall(new Name('back'), $node->args);
    }

    private function isNotRedirectBackMethodCall(MethodCall $node): bool
    {
        if (! $node->var instanceof FuncCall) {
            return true;
        }

        if (count($node->var->getArgs()) > 0) {
            return true;
        }

        if ($this->getName($node->var->name) !== 'redirect') {
            return true;
        }

        return false;
    }

    private function isNotRedirectBackStaticCall(Node $node): bool
    {
        if (! $this->isName($node->class, 'Illuminate\Support\Facades\Redirect')) {
            return true;
        }

        if (! $this->isName($node->name, 'back')) {
            return true;
        }

        return false;
    }
}
