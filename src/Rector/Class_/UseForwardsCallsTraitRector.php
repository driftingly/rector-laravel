<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitor;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\CallUserFuncAnalyzer;
use RectorLaravel\ValueObject\ForwardingCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\UseForwardsCallsTraitRector\UseForwardsCallsTraitRectorTest
 */
final class UseForwardsCallsTraitRector extends AbstractRector
{
    /**
     * @readonly
     */
    private CallUserFuncAnalyzer $callUserFuncAnalyzer;
    /**
     * @var string
     */
    private const FORWARD_CALLS_TRAIT = 'Illuminate\Support\Traits\ForwardsCalls';

    public function __construct(CallUserFuncAnalyzer $callUserFuncAnalyzer)
    {
        $this->callUserFuncAnalyzer = $callUserFuncAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces the use of `call_user_func` and `call_user_func_array` method with the CallForwarding trait',
            [new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->foo, $method], $parameters);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
class SomeClass
{
    use ForwardsCalls;

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->foo, $method, $parameters);
    }
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->hasCallUserFunctionCalls($node)) {
            return null;
        }

        $this->addCallForwardingTrait($node);

        $this->refactorFunctionCalls($node);

        return $node;
    }

    public function hasCallUserFunctionCalls(Class_ $class): bool
    {
        $found = false;

        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use (&$found): ?int {
            if (! $node instanceof FuncCall) {
                return null;
            }

            if ($node->isFirstClassCallable()) {
                return null;
            }

            if (
                $this->callUserFuncAnalyzer->isCallUserFuncCall($node)
                && $this->callUserFuncAnalyzer->canDetermineMethodFromCallable($node)
            ) {
                $found = true;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $found;
    }

    private function addCallForwardingTrait(Class_ $class): void
    {
        $traitUses = $class->getTraitUses();

        if (count($traitUses) > 0) {
            foreach ($traitUses as $traitUse) {
                foreach ($traitUse->traits as $trait) {
                    if ($this->isName($trait, self::FORWARD_CALLS_TRAIT)) {
                        return;
                    }
                }
            }
            $traitUses[0]->traits[] = new FullyQualified(self::FORWARD_CALLS_TRAIT);

            return;
        }

        $class->stmts = array_merge([new TraitUse([new FullyQualified(self::FORWARD_CALLS_TRAIT)])], $class->stmts);
    }

    private function refactorFunctionCalls(Class_ $class): void
    {
        $this->traverseNodesWithCallable($class->stmts, function (Node $node): ?Node {
            if (! $node instanceof FuncCall) {
                return null;
            }

            if ($this->callUserFuncAnalyzer->isCallUserFuncCall($node)) {
                $forwardCall = $this->callUserFuncAnalyzer->getForwardedMethod($node);

                if (! $forwardCall instanceof ForwardingCall) {
                    return null;
                }

                return new MethodCall(
                    new Variable('this'),
                    'forwardCallTo',
                    [
                        new Arg($forwardCall->getObject()),
                        new Arg($forwardCall->getMethod()),
                        new Arg($forwardCall->getArgs()),
                    ]
                );
            }

            return null;
        });
    }
}
