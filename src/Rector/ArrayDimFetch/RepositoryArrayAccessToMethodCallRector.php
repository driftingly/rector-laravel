<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Unset_;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\Tests\Rector\ArrayDimFetch\RepositoryArrayAccessToMethodCallRector\RepositoryArrayAccessToMethodCallRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RepositoryArrayAccessToMethodCallRectorTest
 */
final class RepositoryArrayAccessToMethodCallRector extends AbstractRector
{
    /**
     * Config and cache repository contracts do not expose array access, only their concrete
     * classes do. Mapped to the methods that replace read / assign / isset / unset.
     *
     * @var array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    private const array TYPE_TO_METHODS = [
        'Illuminate\Contracts\Config\Repository' => ['get', 'set', 'has', 'set'],
        'Illuminate\Contracts\Cache\Repository' => ['get', 'set', 'has', 'forget'],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change array access on a config or cache repository to the matching method call, as the contracts do not extend ArrayAccess',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$config['app.name'];
$config['app.name'] = 'Foo';
isset($config['app.name']);
unset($config['app.name']);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$config->get('app.name');
$config->set('app.name', 'Foo');
$config->has('app.name');
$config->set('app.name');
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class, Isset_::class, Unset_::class, ArrayDimFetch::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Assign) {
            return $this->refactorAssign($node);
        }

        if ($node instanceof Isset_) {
            return $this->refactorIsset($node);
        }

        if ($node instanceof Unset_) {
            return $this->refactorUnset($node);
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

        $methods = $this->matchMethods($arrayDimFetch->var);
        if ($methods === null) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($arrayDimFetch->var, $methods[0], [$arrayDimFetch->dim]);
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

        $methods = $this->matchMethods($arrayDimFetch->var);
        if ($methods === null) {
            return null;
        }

        return $this->nodeFactory->createMethodCall(
            $arrayDimFetch->var,
            $methods[1],
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

        $methods = $this->matchMethods($var->var);
        if ($methods === null) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($var->var, $methods[2], [$var->dim]);
    }

    private function refactorUnset(Unset_ $unset): ?Expression
    {
        if (count($unset->vars) !== 1) {
            return null;
        }

        $var = $unset->vars[0];
        if (! $var instanceof ArrayDimFetch || ! $var->dim instanceof Expr) {
            return null;
        }

        $methods = $this->matchMethods($var->var);
        if ($methods === null) {
            return null;
        }

        return new Expression($this->nodeFactory->createMethodCall($var->var, $methods[3], [$var->dim]));
    }

    /**
     * @return array{0: string, 1: string, 2: string, 3: string}|null
     */
    private function matchMethods(Expr $expr): ?array
    {
        foreach (self::TYPE_TO_METHODS as $type => $methods) {
            if ($this->isObjectType($expr, new ObjectType($type))) {
                return $methods;
            }
        }

        return null;
    }
}
