<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector\DatabaseExpressionToStringToMethodCallRectorTest
 */
final class DatabaseExpressionToStringToMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert DB Expression __toString() calls to getValue() method calls.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\DB;

$string = DB::raw('select 1')->__toString();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\DB;

$string = DB::raw('select 1')->getValue(DB::connection()->getQueryGrammar());
CODE_SAMPLE
            ),
        ]);
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
        if (! $this->isName($node->name, '__toString')) {
            return null;
        }

        if (! $node->var instanceof StaticCall) {
            return null;
        }

        if (! $this->isName($node->var->class, 'Illuminate\Support\Facades\DB')) {
            return null;
        }

        if (! $this->isName($node->var->name, 'raw')) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($node->var, 'getValue', [
            $this->nodeFactory->createMethodCall(
                $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\DB', 'connection'),
                'getQueryGrammar'
            ),
        ]);
    }
}
