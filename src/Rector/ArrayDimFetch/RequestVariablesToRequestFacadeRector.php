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
use RectorLaravel\ValueObject\ReplaceRequestKeyAndMethodValue;
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
$_REQUEST['value'];
$_POST;
$_GET;
$_REQUEST;
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Request::query('value');
\Illuminate\Support\Facades\Request::post('value');
\Illuminate\Support\Facades\Request::input('value');
\Illuminate\Support\Facades\Request::query();
\Illuminate\Support\Facades\Request::post();
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
    public function refactor(Node $node)
    {
        if ($node instanceof Variable) {
            return $this->processVariable($node);
        }

        $replaceValue = $this->findAllKeys($node);

        if ($replaceValue instanceof ReplaceRequestKeyAndMethodValue) {
            return $this->nodeFactory->createStaticCall(
                'Illuminate\Support\Facades\Request',
                $replaceValue->getMethod(),
                [new Arg(new String_($replaceValue->getKey()))]
            );
        }

        return $replaceValue;
    }

    /**
     * @return ReplaceRequestKeyAndMethodValue|1|null
     */
    public function findAllKeys(ArrayDimFetch $arrayDimFetch)
    {
        if (! $arrayDimFetch->dim instanceof Scalar) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        $value = $this->getType($arrayDimFetch->dim)->getConstantScalarValues()[0] ?? null;

        if ($value === null) {
            return null;
        }

        if ($arrayDimFetch->var instanceof ArrayDimFetch) {
            $replaceValue = $this->findAllKeys($arrayDimFetch->var);

            if (! $replaceValue instanceof ReplaceRequestKeyAndMethodValue) {
                return $replaceValue;
            }

            return new ReplaceRequestKeyAndMethodValue(implode('.', [$replaceValue->getKey(), $value]), $replaceValue->getMethod());
        }

        if ($this->isNames($arrayDimFetch->var, ['_GET', '_POST', '_REQUEST'])) {
            if (! $arrayDimFetch->var instanceof Variable) {
                return null;
            }

            switch ($arrayDimFetch->var->name) {
                case '_GET':
                    $method = 'query';
                    break;
                case '_POST':
                    $method = 'post';
                    break;
                case '_REQUEST':
                    $method = 'input';
                    break;
                default:
                    $method = null;
                    break;
            }

            if ($method === null) {
                return null;
            }

            return new ReplaceRequestKeyAndMethodValue((string) $value, $method);
        }

        return null;
    }

    private function processVariable(Variable $variable): ?StaticCall
    {
        switch ($variable->name) {
            case '_GET':
                $method = 'query';
                break;
            case '_POST':
                $method = 'post';
                break;
            case '_REQUEST':
                $method = 'all';
                break;
            default:
                $method = null;
                break;
        }

        if ($method === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall(
            'Illuminate\Support\Facades\Request',
            $method
        );
    }
}
