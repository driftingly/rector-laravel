<?php

namespace RectorLaravel\Rector\Coalesce;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use RectorLaravel\ValueObject\ApplyDefaultWithFuncCall;
use RectorLaravel\ValueObject\ApplyDefaultWithMethodCall;
use RectorLaravel\ValueObject\ApplyDefaultWithStaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
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
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
config('app.name') ?? 'Laravel';
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
config('app.name', 'Laravel');
CODE_SAMPLE
                    , [
                        new ApplyDefaultWithFuncCall('config'),
                    ]),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Coalesce::class];
    }

    /**
     * @param  Coalesce  $node
     */
    public function refactor(Node $node): MethodCall|StaticCall|FuncCall|null
    {
        if (! $node->left instanceof FuncCall &&
            ! $node->left instanceof MethodCall &&
            ! $node->left instanceof StaticCall
        ) {
            return null;
        }

        if ($node->left->isFirstClassCallable()) {
            return null;
        }

        $call = $node->left;

        foreach ($this->applyDefaultWith as $applyDefaultWith) {
            $valid = false;

            if ($applyDefaultWith instanceof ApplyDefaultWithFuncCall &&
                $call instanceof FuncCall && $this->isName($call, $applyDefaultWith->getFunctionName())) {
                $valid = true;
            } elseif (
                $applyDefaultWith instanceof ApplyDefaultWithMethodCall &&
                $call instanceof MethodCall &&
                $this->isObjectType($call->var, $applyDefaultWith->getObjectType()) &&
                $this->isName($call->name, $applyDefaultWith->getMethodName())
            ) {
                $valid = true;
            } elseif (
                $applyDefaultWith instanceof ApplyDefaultWithStaticCall &&
                $call instanceof StaticCall &&
                $this->isObjectType($call->class, $applyDefaultWith->getObjectType()) &&
                $this->isName($call->name, $applyDefaultWith->getMethodName())
            ) {
                $valid = true;
            }

            if (! $valid) {
                continue;
            }

            if (count($call->args) === $applyDefaultWith->getArgumentPosition()) {
                if ($this->getType($node->right)->isNull()->yes()) {
                    return $call;
                }
                $call->args[count($call->args)] = new Arg($node->right);

                return $call;
            } elseif (count($call->args) === ($applyDefaultWith->getArgumentPosition() + 1)) {
                return $call;
            }
        }

        return null;
    }

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
