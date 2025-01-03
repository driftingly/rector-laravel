<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitor;
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
$_SESSION['key'];
$_SESSION['key'] = 'value';
$_SESSION;
session_regenerate_id();
session_unset();
session_destroy();
session_start();
unset($_SESSION['key']);
isset($_SESSION['key'])
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Session::get('key');
\Illuminate\Support\Facades\Session::put('key', 'value');
\Illuminate\Support\Facades\Session::all();
\Illuminate\Support\Facades\Session::regenerate();
\Illuminate\Support\Facades\Session::flush();
\Illuminate\Support\Facades\Session::destroy();
\Illuminate\Support\Facades\Session::start();
\Illuminate\Support\Facades\Session::forget('key');
\Illuminate\Support\Facades\Session::has('key');
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [
            Isset_::class,
            Unset_::class,
            ArrayDimFetch::class,
            Assign::class,
            FuncCall::class,
            Variable::class,
        ];
    }

    /**
     * @param  ArrayDimFetch|Assign|FuncCall|Isset_|Unset_|Variable  $node
     * @return StaticCall|Expression|1|null
     */
    public function refactor(Node $node)
    {
        if ($node instanceof ArrayDimFetch) {
            return $this->processDimFetch($node);
        }

        if ($node instanceof FuncCall) {
            return $this->processFunction($node);
        }

        if ($node instanceof Isset_) {
            return $this->processIsset($node);
        }

        if ($node instanceof Unset_) {
            $return = $this->processUnset($node);
            if ($return instanceof StaticCall) {
                return new Expression($return);
            }

            return $return;
        }

        if ($node instanceof Variable) {
            return $this->processVariable($node);
        }

        return $this->processAssign($node);
    }

    /**
     * @return StaticCall|1|null
     */
    public function processDimFetch(ArrayDimFetch $arrayDimFetch)
    {
        if (! $this->isName($arrayDimFetch->var, '_SESSION')) {
            return null;
        }

        if (! $arrayDimFetch->dim instanceof Expr) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'get', [
            new Arg($arrayDimFetch->dim),
        ]);
    }

    private function processAssign(Assign $assign): ?StaticCall
    {
        $dimFetch = $assign->var;

        if (! $dimFetch instanceof ArrayDimFetch || ! $this->isName($dimFetch->var, '_SESSION')) {
            return null;
        }

        if (! $dimFetch->dim instanceof Expr) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'put', [
            new Arg($dimFetch->dim),
            new Arg($assign->expr),
        ]);
    }

    private function processFunction(FuncCall $funcCall): ?StaticCall
    {
        if (! $this->isNames($funcCall, [
            'session_regenerate_id',
            'session_unset',
            'session_destroy',
            'session_start',
        ])) {
            return null;
        }

        $method = $this->getName($funcCall);
        switch ($method) {
            case 'session_regenerate_id':
                $replacementMethod = 'regenerate';
                break;
            case 'session_unset':
                $replacementMethod = 'flush';
                break;
            case 'session_destroy':
                $replacementMethod = 'destroy';
                break;
            case 'session_start':
                $replacementMethod = 'start';
                break;
            default:
                $replacementMethod = null;
                break;
        }

        if ($replacementMethod === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', $replacementMethod);
    }

    /**
     * @return StaticCall|1|null
     */
    private function processIsset(Isset_ $isset)
    {
        if (count($isset->vars) < 1) {
            return null;
        }

        $var = $isset->vars[0];

        if (! $var instanceof ArrayDimFetch) {
            return null;
        }

        if (! $this->isName($var->var, '_SESSION')) {
            return null;
        }

        if (! $var->dim instanceof Expr) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'has', [
            new Arg($var->dim),
        ]);
    }

    /**
     * @return StaticCall|1|null
     */
    private function processUnset(Unset_ $unset)
    {
        if (count($unset->vars) < 1) {
            return null;
        }

        $var = $unset->vars[0];

        if (! $var instanceof ArrayDimFetch) {
            return null;
        }

        if (! $this->isName($var->var, '_SESSION')) {
            return null;
        }

        if (! $var->dim instanceof Expr) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'forget', [
            new Arg($var->dim),
        ]);
    }

    private function processVariable(Variable $variable): ?StaticCall
    {
        if (! $this->isName($variable, '_SESSION')) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Session', 'all');
    }
}
