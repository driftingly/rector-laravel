<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use RectorLaravel\ValueObject\AddArgumentDefaultValue;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\ClassMethod\AddArgumentDefaultValueRector\AddArgumentDefaultValueRectorTest
 */
final class AddArgumentDefaultValueRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const ADDED_ARGUMENTS = 'added_arguments';

    /**
     * @var AddArgumentDefaultValue[]
     */
    private $addedArguments = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds default value for arguments in defined methods.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod($value)
    {
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod($value = false)
    {
    }
}
CODE_SAMPLE
                    ,
                    [
                        self::ADDED_ARGUMENTS => [
                            new AddArgumentDefaultValue('SomeClass', 'someMethod', 0, false),
                        ],
                    ]
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param  ClassLike  $node
     */
    public function refactor(Node $node): ?ClassLike
    {
        $hasChanged = false;
        foreach ($this->addedArguments as $addedArgument) {
            if (! $this->nodeTypeResolver->isObjectType($node, $addedArgument->getObjectType())) {
                continue;
            }

            foreach ($node->getMethods() as $classMethod) {
                if (! $this->isName($classMethod->name, $addedArgument->getMethod())) {
                    continue;
                }

                if (! isset($classMethod->params[$addedArgument->getPosition()])) {
                    continue;
                }

                $position = $addedArgument->getPosition();
                $param = $classMethod->params[$position];

                if ($param->default instanceof Expr) {
                    continue;
                }

                $classMethod->params[$position] = new Param($param->var, BuilderHelpers::normalizeValue(
                    $addedArgument->getDefaultValue()
                ));

                $hasChanged = true;
            }
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $addedArguments = $configuration[self::ADDED_ARGUMENTS] ?? $configuration;
        Assert::isArray($addedArguments);
        Assert::allIsInstanceOf($addedArguments, AddArgumentDefaultValue::class);

        $this->addedArguments = $addedArguments;
    }
}
