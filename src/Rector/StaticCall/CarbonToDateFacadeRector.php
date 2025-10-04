<?php

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\CarbonToDateFacadeRectorTest
 */
final class CarbonToDateFacadeRector extends AbstractRector
{
    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Refactor Carbon static method calls to use the Date facade instead.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Carbon\Carbon;

Carbon::now();
Carbon::parse('2024-01-01');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Date;

Date::now();
Date::parse('2024-01-01');
CODE_SAMPLE
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Carbon;

Carbon::now();
Carbon::today();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Date;

Date::now();
Date::today();
CODE_SAMPLE
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?StaticCall
    {
        if (! $node instanceof StaticCall) {
            return null;
        }

        if (! $this->isCarbon($node->class)) {
            return null;
        }

        return new StaticCall(
            new FullyQualified('Illuminate\Support\Facades\Date'),
            $node->name,
            $node->args,
            $node->getAttributes()
        );
    }

    private function isCarbon(Node $node): bool
    {
        return $this->isObjectType($node, new ObjectType('Carbon\Carbon')) ||
            $this->isObjectType($node, new ObjectType('Illuminate\Support\Carbon'));
    }
}
