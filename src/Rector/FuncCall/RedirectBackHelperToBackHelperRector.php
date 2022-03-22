<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
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
class SomeClass
{
    public function store()
    {
        return redirect()->back()->with('error', 'Incorrect Details.')
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
     * @param $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, self::BACK_KEYWORD) || ! $node instanceof MethodCall) {
            return null;
        }

        $rootExpr = $this->fluentChainMethodCallNodeAnalyzer->resolveRootExpr($node);
        $parentNode = $rootExpr->getAttribute(AttributeKey::PARENT_NODE);

        if ($this->shouldSkip($parentNode)) {
            return null;
        }

        $this->removeNode($node);

        $parentNode->var->name = new Name(self::BACK_KEYWORD);

        return $parentNode;
    }

    private function shouldSkip(MethodCall|FuncCall $node): bool
    {
        if (! $node->var instanceof FuncCall) {
            return true;
        }

        if (count($node->var->getArgs()) > 0) {
            return true;
        }

        if ($this->getName($node->var->name) !== self::REDIRECT_KEYWORD) {
            return true;
        }

        return false;
    }
}
