<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Cast;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\StaticCall;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCallRector\DatabaseExpressionCastsToMethodCallRectorTest
 */
final class DatabaseExpressionCastsToMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert DB Expression string casts to getValue() method calls.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\DB;

$string = (string) DB::raw('select 1');
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
        return [String_::class];
    }

    /**
     * @param  String_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->expr instanceof StaticCall) {
            return null;
        }

        if (! $this->isName($node->expr->class, 'Illuminate\Support\Facades\DB')) {
            return null;
        }

        if (! $this->isName($node->expr->name, 'raw')) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($node->expr, 'getValue', [
            $this->nodeFactory->createMethodCall(
                $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\DB', 'connection'),
                'getQueryGrammar'
            ),
        ]);
    }
}
