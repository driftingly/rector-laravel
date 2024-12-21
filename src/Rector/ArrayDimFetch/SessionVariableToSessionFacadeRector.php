<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\StaticCall;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\SessionVariableToSessionFacadeRectorTest
 */
class SessionVariableToSessionFacadeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change PHP session usage to Session Facade methods',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$_SESSION['VARIABLE'];
$_SESSION['VARIABLE'] = 'value';
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Session::get('VARIABLE');
\Illuminate\Support\Facades\Session::put('VARIABLE', 'value');
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class, Assign::class];
    }

    /**
     * @param  ArrayDimFetch|Assign  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if ($node instanceof ArrayDimFetch) {
            return $this->processDimFetch($node);
        }

        return $this->processAssign($node);
    }

    public function processDimFetch(ArrayDimFetch $node): ?StaticCall
    {
        if (! $this->isName($node->var, '_SESSION')) {
            return null;
        }

        if ($node->dim === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'get', [
            new Arg($node->dim),
        ]);
    }

    private function processAssign(Assign $node): ?StaticCall
    {
        $dimFetch = $node->var;

        if (! $dimFetch instanceof ArrayDimFetch || ! $this->isName($dimFetch->var, '_SESSION')) {
            return null;
        }

        if ($dimFetch->dim === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'put', [
            new Arg($dimFetch->dim),
            new Arg($node->expr),
        ]);
    }
}
