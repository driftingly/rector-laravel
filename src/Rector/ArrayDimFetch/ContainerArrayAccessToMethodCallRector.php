<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\Tests\Rector\ArrayDimFetch\ContainerArrayAccessToMethodCallRector\ContainerArrayAccessToMethodCallRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see ContainerArrayAccessToMethodCallRectorTest
 */
final class ContainerArrayAccessToMethodCallRector extends AbstractRector
{
    /**
     * @var string
     */
    private const CONTAINER_CONTRACT = 'Illuminate\Contracts\Container\Container';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change array access on the container to the matching method call, as the contract does not extend ArrayAccess',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$container['foo'];
$container['foo'] = 'Foo';
isset($container['foo']);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$container->make('foo');
$container->bind('foo', 'Foo');
$container->bound('foo');
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class, Isset_::class, ArrayDimFetch::class];
    }

    public function refactor(Node $node): ?MethodCall
    {
        if ($node instanceof Assign) {
            return $this->refactorAssign($node);
        }

        if ($node instanceof Isset_) {
            return $this->refactorIsset($node);
        }

        if ($node instanceof ArrayDimFetch) {
            return $this->refactorRead($node);
        }

        return null;
    }

    private function refactorRead(ArrayDimFetch $arrayDimFetch): ?MethodCall
    {
        // writes are handled through their parent node
        if ($arrayDimFetch->getAttribute(ArrayDimFetchContextNodeVisitor::IS_IN_WRITE_CONTEXT) === true) {
            return null;
        }

        if (! $arrayDimFetch->dim instanceof Expr) {
            return null;
        }

        if (! $this->isContainer($arrayDimFetch->var)) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($arrayDimFetch->var, 'make', [$arrayDimFetch->dim]);
    }

    private function refactorAssign(Assign $assign): ?MethodCall
    {
        if (! $assign->var instanceof ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $assign->var;
        if (! $arrayDimFetch->dim instanceof Expr) {
            return null;
        }

        if (! $this->isContainer($arrayDimFetch->var)) {
            return null;
        }

        return $this->nodeFactory->createMethodCall(
            $arrayDimFetch->var,
            'bind',
            [$arrayDimFetch->dim, $assign->expr]
        );
    }

    private function refactorIsset(Isset_ $isset): ?MethodCall
    {
        if (count($isset->vars) !== 1) {
            return null;
        }

        $var = $isset->vars[0];
        if (! $var instanceof ArrayDimFetch || ! $var->dim instanceof Expr) {
            return null;
        }

        if (! $this->isContainer($var->var)) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($var->var, 'bound', [$var->dim]);
    }

    private function isContainer(Expr $expr): bool
    {
        return $this->isObjectType($expr, new ObjectType(self::CONTAINER_CONTRACT));
    }
}
