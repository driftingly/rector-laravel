<?php

namespace RectorLaravel\Rector\Coalesce;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Throw_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\ValueObject\ApplyDefaultInsteadOfNullCoalesce;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\ApplyDefaultInsteadOfNullCoalesceRectorTest
 */
final class ApplyDefaultInsteadOfNullCoalesceRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ApplyDefaultInsteadOfNullCoalesce[]
     */
    private array $applyDefaultWith;

    public function __construct()
    {
        $this->applyDefaultWith = self::defaultLaravelMethods();
    }

    /**
     * @return ApplyDefaultInsteadOfNullCoalesce[]
     */
    public static function defaultLaravelMethods(): array
    {
        return [
            new ApplyDefaultInsteadOfNullCoalesce('config'),
            new ApplyDefaultInsteadOfNullCoalesce('env'),
            new ApplyDefaultInsteadOfNullCoalesce('data_get', null, 2),
            new ApplyDefaultInsteadOfNullCoalesce('input', new ObjectType('Illuminate\Http\Request')),
            new ApplyDefaultInsteadOfNullCoalesce('get', new ObjectType('Illuminate\Support\Env')),
        ];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Apply default instead of null coalesce',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
custom_helper('app.name') ?? 'Laravel';
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
custom_helper('app.name', 'Laravel');
CODE_SAMPLE
                    ,
                    array_merge(self::defaultLaravelMethods(), [new ApplyDefaultInsteadOfNullCoalesce('custom_helper')]),
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Coalesce::class];
    }

    /**
     * @param  Coalesce  $node
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\FuncCall|null
     */
    public function refactor(Node $node)
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

        if ($call instanceof MethodCall) {
            $objectType = $call->var;
        } elseif ($call instanceof StaticCall) {
            $objectType = $call->class;
        } else {
            $objectType = null;
        }

        foreach ($this->applyDefaultWith as $applyDefaultWith) {
            $valid = false;

            if (
                $applyDefaultWith->getObjectType() !== null &&
                $objectType instanceof Node &&
                $this->isObjectType(
                    $objectType,
                    $applyDefaultWith->getObjectType()) &&
                $this->isName($call->name, $applyDefaultWith->getMethodName()) &&
                ! $node->right instanceof Throw_
            ) {
                $valid = true;
            } elseif (
                $applyDefaultWith->getObjectType() === null &&
                $this->isName($call->name, $applyDefaultWith->getMethodName()) &&
                ! $node->right instanceof Throw_
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
            }
        }

        return null;
    }

    public function configure(array $configuration): void
    {
        Assert::allIsInstanceOf($configuration, ApplyDefaultInsteadOfNullCoalesce::class);
        $this->applyDefaultWith = $configuration;
    }
}
