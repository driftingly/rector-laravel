<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
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
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Request::input('value');
\Illuminate\Support\Facades\Request::input('value');
CODE_SAMPLE
                ),
            ]
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
        $key = $this->findAllKeys($node);

        if (! is_string($key)) {
            return null;
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
}
