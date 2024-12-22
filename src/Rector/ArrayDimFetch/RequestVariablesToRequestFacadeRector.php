<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitor;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\RequestVariablesToRequestFacadeRectorTest
 */
class RequestVariablesToRequestFacadeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change request variable definition in Facade',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$_GET['value'];
$_POST['value'];
$_POST;
$_GET;
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Request::input('value');
\Illuminate\Support\Facades\Request::input('value');
\Illuminate\Support\Facades\Request::all();
\Illuminate\Support\Facades\Request::all();
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class, Variable::class];
    }

    /**
     * @param  ArrayDimFetch|Variable  $node
     * @return StaticCall|1|null
     */
    public function refactor(Node $node): StaticCall|int|null
    {
        if ($node instanceof Variable) {
            return $this->processVariable($node);
        }

        $key = $this->findAllKeys($node);

        if (! is_string($key)) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        return $this->nodeFactory->createStaticCall(
            'Illuminate\Support\Facades\Request',
            'input',
            [new Arg(new String_($key))]
        );
    }

    public function findAllKeys(ArrayDimFetch $arrayDimFetch): ?string
    {
        if (! $arrayDimFetch->dim instanceof Scalar) {
            return null;
        }

        $value = $this->getType($arrayDimFetch->dim)->getConstantScalarValues()[0] ?? null;

        if ($value === null) {
            return null;
        }

        if ($arrayDimFetch->var instanceof ArrayDimFetch) {
            $key = $this->findAllKeys($arrayDimFetch->var);

            if ($key === null) {
                return null;
            }

            return implode('.', [$key, $value]);
        }

        if ($this->isNames($arrayDimFetch->var, ['_GET', '_POST'])) {
            return (string) $value;
        }

        return null;
    }

    private function processVariable(Variable $variable): ?StaticCall
    {
        if ($this->isNames($variable, ['_GET', '_POST'])) {
            return $this->nodeFactory->createStaticCall(
                'Illuminate\Support\Facades\Request',
                'all',
            );
        }

        return null;
    }
}
