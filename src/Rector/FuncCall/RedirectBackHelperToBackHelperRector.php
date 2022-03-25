<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use Rector\Defluent\NodeAnalyzer\FluentChainMethodCallNodeAnalyzer;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\FuncCall\RedirectBackHelperToBackHelperRector\RedirectBackHelperToBackHelperRectorTest
 */

final class RedirectBackHelperToBackHelperRector extends AbstractRector
{
    /**
     * @var string
     */
    private const BACK_KEYWORD = 'back';

    /**
     * @var string
     */
    private const REDIRECT_KEYWORD = 'redirect';

    public function __construct(
        private readonly FluentChainMethodCallNodeAnalyzer $fluentChainMethodCallNodeAnalyzer,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change redirect()->back() method to back()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Redirect;

class SomeClass
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
class SomeClass
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
            if (! $this->isName($node->name, self::BACK_KEYWORD)) {
                return null;
            }

            $rootExpr = $this->fluentChainMethodCallNodeAnalyzer->resolveRootExpr($node);
            $parentNode = $rootExpr->getAttribute(AttributeKey::PARENT_NODE);

            if (! $this->isPatternMatch($parentNode)) {
                return null;
            }

            $this->removeNode($node);

            $parentNode->var->name = new Name(self::BACK_KEYWORD);

            return $parentNode;
        }

        if (! $this->isStaticCallPatternMatch($node)) {
            return null;
        }
        return new FuncCall(new Name('back'), $node->args);
    }

    private function isPatternMatch(MethodCall $node): bool
    {
        if (! $node->var instanceof FuncCall) {
            return false;
        }

        if (count($node->var->getArgs()) > 0) {
            return false;
        }

        if ($this->getName($node->var->name) !== self::REDIRECT_KEYWORD) {
            return false;
        }

        return true;
    }

    public function isStaticCallPatternMatch(Node $node): bool
    {
        if (! $this->isName($node->class, 'Illuminate\Support\Facades\Redirect')) {
            return false;
        }

        if (! $this->isName($node->name, 'back')) {
            return false;
        }

        return true;
    }
}
