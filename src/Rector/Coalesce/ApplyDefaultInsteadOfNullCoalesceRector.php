<?php

namespace RectorLaravel\Rector\Coalesce;

use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use RectorLaravel\ValueObject\ApplyDefaultWithFuncCall;
use RectorLaravel\ValueObject\ApplyDefaultWithMethodCall;
use RectorLaravel\ValueObject\ApplyDefaultWithStaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\ApplyDefaultInsteadOfNullCoalesceRectorTest
 */
final class ApplyDefaultInsteadOfNullCoalesceRector extends AbstractRector implements ConfigurableRectorInterface
{

    /**
     * @var array<int, ApplyDefaultWithFuncCall|ApplyDefaultWithMethodCall|ApplyDefaultWithStaticCall>
     */
    private array $applyDefaultWith;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Apply default instead of null coalesce',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
config('app.name') ?? 'Laravel';
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
config('app.name', 'Laravel');
CODE_SAMPLE
                )
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\BinaryOp\Coalesce::class];
    }

    /**
     * @param Node\Expr\BinaryOp\Coalesce $node
     * @return Node\Expr\MethodCall|Node\Expr\StaticCall|Node\Expr\FuncCall|null
     */
    public function refactor(Node $node): Node\Expr\MethodCall|Node\Expr\StaticCall|Node\Expr\FuncCall|null
    {
        foreach ($this->applyDefaultWith as $applyDefaultWith) {
            if (! $applyDefaultWith instanceof ApplyDefaultWithFuncCall || ! $node->left instanceof Node\Expr\FuncCall) {
                return null;
            }

            if (! $this->isName($node->left, $applyDefaultWith->getFunctionName())) {
                return null;
            }

            $call = $node->left;

            if ($call->isFirstClassCallable()) {
                return null;
            }

            if (count($call->args) === $applyDefaultWith->getArgumentPosition()) {
                if ($this->getType($node->right)->isNull()->yes()) {
                    return $call;
                }
                $call->args[count($call->args)] = new Node\Arg($node->right);

                return $call;
            }
        }

        return null;
    }

    /**
     * @param array<int, ApplyDefaultWithFuncCall|ApplyDefaultWithStaticCall|ApplyDefaultWithMethodCall> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsInstanceOfAny($configuration, [
            ApplyDefaultWithFuncCall::class,
            ApplyDefaultWithMethodCall::class,
            ApplyDefaultWithStaticCall::class,
        ]);
        $this->applyDefaultWith = $configuration;
    }
}
