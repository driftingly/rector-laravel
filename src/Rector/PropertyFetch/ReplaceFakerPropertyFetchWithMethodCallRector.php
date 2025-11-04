<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\PropertyFetch;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector\ReplaceFakerInstanceWithHelperRectorTest
 */
final class ReplaceFakerPropertyFetchWithMethodCallRector extends AbstractRector
{
    /**
     * @readonly
     */
    private BuilderFactory $builderFactory;
    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated faker property fetch with method call',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$faker->name,
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$faker->name(),
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param  PropertyFetch  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node->var, new ObjectType('Faker\Generator'))) {
            return null;
        }

        return $this->builderFactory->methodCall($node->var, $node->name);
    }
}
