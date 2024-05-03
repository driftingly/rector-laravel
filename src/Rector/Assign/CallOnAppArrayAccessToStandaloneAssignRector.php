<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Assign;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Type\ObjectType;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use RectorLaravel\NodeFactory\AppAssignFactory;
use RectorLaravel\ValueObject\ServiceNameTypeAndVariableName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector\CallOnAppArrayAccessToStandaloneAssignRectorTest
 */
final class CallOnAppArrayAccessToStandaloneAssignRector extends AbstractRector
{
    /**
     * @readonly
     * @var \RectorLaravel\NodeFactory\AppAssignFactory
     */
    private $appAssignFactory;
    /**
     * @readonly
     * @var \Rector\Comments\NodeDocBlock\DocBlockUpdater
     */
    private $docBlockUpdater;
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @var ServiceNameTypeAndVariableName[]
     */
    private $serviceNameTypeAndVariableNames = [];

    public function __construct(AppAssignFactory $appAssignFactory, DocBlockUpdater $docBlockUpdater, ValueResolver $valueResolver)
    {
        $this->appAssignFactory = $appAssignFactory;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->valueResolver = $valueResolver;
        $this->serviceNameTypeAndVariableNames[] = new ServiceNameTypeAndVariableName(
            'validator',
            'Illuminate\Validation\Factory',
            'validationFactory'
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param  Expression  $node
     * @return \PhpParser\Node|mixed[]|int|null
     */
    public function refactor(Node $node)
    {
        if (! $node->expr instanceof Assign) {
            return null;
        }

        if (! $node->expr->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $node->expr->expr;
        if (! $methodCall->var instanceof ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $methodCall->var;

        if (! $this->isObjectType(
            $arrayDimFetch->var,
            new ObjectType('Illuminate\Contracts\Foundation\Application')
        )) {
            return null;
        }

        $arrayDimFetchDim = $methodCall->var->dim;
        if (! $arrayDimFetchDim instanceof Expr) {
            return null;
        }

        foreach ($this->serviceNameTypeAndVariableNames as $serviceNameTypeAndVariableName) {
            if (! $this->valueResolver->isValue($arrayDimFetchDim, $serviceNameTypeAndVariableName->getServiceName())) {
                continue;
            }

            $assignExpression = $this->appAssignFactory->createAssignExpression(
                $serviceNameTypeAndVariableName,
                $methodCall->var
            );

            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($assignExpression);

            $methodCall->var = new Variable($serviceNameTypeAndVariableName->getVariableName());

            // the nop is a workaround because the docs of the first node are somehow stripped away
            // this will add a newline but the docs will be preserved
            return [new Nop, $assignExpression, $node];
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace magical call on $this->app["something"] to standalone type assign variable',
            [new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    public function run()
    {
        $validator = $this->app['validator']->make('...');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    public function run()
    {
        /** @var \Illuminate\Validation\Factory $validationFactory */
        $validationFactory = $this->app['validator'];
        $validator = $validationFactory->make('...');
    }
}
CODE_SAMPLE
            )]
        );
    }
}
