<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\Defluent\NodeAnalyzer\FluentChainMethodCallNodeAnalyzer;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\FuncCall\RedirectBackHelperToBackHelperRector\RedirectBackHelperToBackHelperRectorTest
 */

final class RedirectBackHelperToBackHelperRector extends AbstractRector
{
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
        return [MethodCall::class, FuncCall::class];
    }

    /**
     * @param $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall && $this->isName($node->name, 'back')) {
            $rootExpr = $this->fluentChainMethodCallNodeAnalyzer->resolveRootExpr($node);
            $callerNode = $rootExpr->getAttribute(AttributeKey::PARENT_NODE);

            if (! $callerNode->var instanceof FuncCall) {
                return null;
            }

            if (count($callerNode->var->getArgs()) > 0) {
                return null;
            }

            $this->removeNode($node);

            $callerNode->var->name = new Name('back');

            return $callerNode;
        }

        return null;
    }
}
