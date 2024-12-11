<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\RemoveDumpDataDeadCodeRectorTest
 */
final class RemoveDumpDataDeadCodeRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string[]
     */
    private array $dumpFunctionNames = ['dd', 'dump'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'It will removes the dump data just like dd or dump functions from the code.`',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
class MyController
{
    public function store()
    {
        dd('test');
        return true;
    }

    public function update()
    {
        dump('test');
        return true;
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class MyController
{
    public function store()
    {
        return true;
    }

    public function update()
    {
        return true;
    }
}
CODE_SAMPLE
                    ,
                    ['dd', 'dump'],
                ),
            ]
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
     * @return NodeVisitor::REMOVE_NODE|null
     */
    public function refactor(Node $node): ?int
    {
        if (! $node->expr instanceof FuncCall) {
            return null;
        }

        if (! $this->isNames($node->expr->name, $this->dumpFunctionNames)) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allString($configuration);

        $this->dumpFunctionNames = $configuration;
    }
}
