<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://laravel.com/docs/5.8/upgrade#deferred-service-providers
 *
 * @see \RectorLaravel\Tests\Rector\Class_\PropertyDeferToDeferrableProviderToRector\PropertyDeferToDeferrableProviderToRectorTest
 */
final class PropertyDeferToDeferrableProviderToRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change deprecated $defer = true; to Illuminate\Contracts\Support\DeferrableProvider interface',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\ServiceProvider;

final class SomeServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

final class SomeServiceProvider extends ServiceProvider implements DeferrableProvider
{
}
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
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Support\ServiceProvider'))) {
            return null;
        }

        $deferProperty = $this->matchDeferWithFalseProperty($node);
        if (! $deferProperty instanceof Property) {
            return null;
        }

        unset($node->stmts[array_search($deferProperty, $node->stmts, true)]);

        $node->implements[] = new FullyQualified('Illuminate\Contracts\Support\DeferrableProvider');

        return $node;
    }

    private function matchDeferWithFalseProperty(Class_ $class): ?Property
    {
        foreach ($class->getProperties() as $property) {
            if (! $this->isName($property, 'defer')) {
                continue;
            }

            $onlyProperty = $property->props[0];
            if (! $onlyProperty->default instanceof Expr) {
                return null;
            }

            if ($this->valueResolver->isTrue($onlyProperty->default)) {
                return $property;
            }
        }

        return null;
    }
}
