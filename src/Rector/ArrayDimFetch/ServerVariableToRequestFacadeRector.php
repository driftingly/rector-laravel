<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\ServerVariableToRequestFacadeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see ServerVariableToRequestFacadeRectorTest
 */
final class ServerVariableToRequestFacadeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change server variable to Request facade\'s server method, except in files returning a value like config files, which are loaded before facades are available',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$_SERVER['VARIABLE'];
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facade\Request::server('VARIABLE');
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param  ArrayDimFetch  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->isName($node->var, '_SERVER')) {
            return null;
        }

        if (! $node->dim instanceof Expr) {
            return null;
        }

        if ($node->getAttribute(ArrayDimFetchContextNodeVisitor::IS_IN_SUPERGLOBAL_ASSIGN) === true) {
            return null;
        }

        // skip contexts that require a variable, e.g. $_SERVER['KEY'] .= 'x' or sort($_SERVER['KEY'])
        if ($node->getAttribute(ArrayDimFetchContextNodeVisitor::IS_IN_WRITE_CONTEXT) === true) {
            return null;
        }

        if ($this->isFileReturningValue()) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\Request', 'server', [
            new Arg($node->dim),
        ]);
    }

    /**
     * Files with a return at file scope are loaded for their value, e.g. config/*.php,
     * bootstrap/app.php or the server.php router script. Laravel runs them before the
     * facades are registered, so the Request facade would throw there.
     */
    private function isFileReturningValue(): bool
    {
        $found = false;

        $this->traverseNodesWithCallable($this->getFile()->getNewStmts(), static function (Node $node) use (&$found): ?int {
            // a return inside a function, closure or class does not belong to the file
            if ($node instanceof Expr || $node instanceof FunctionLike || $node instanceof ClassLike) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if ($node instanceof Return_) {
                $found = true;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $found;
    }
}
