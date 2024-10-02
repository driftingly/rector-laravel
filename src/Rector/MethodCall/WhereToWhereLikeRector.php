<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\AssertSeeToAssertSeeHtmlRector\AssertSeeToAssertSeeHtmlRectorTest
 */
final class WhereToWhereLikeRector extends AbstractRector
{
public function __construct(private ValueResolver $valueResolver)
{
}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes `where` method call to `whereLike` method call in Laravel TestResponse',
            [
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Query\Builder'))) {
            return null;
        }

        if (! $this->isName($node->name, 'where')) {
            return null;
        }

        if (count($node->getArgs()) !== 3) {
            return null;
        }

        // if second arg is not 'like' string, skip
        if (! $node->args[1]->value instanceof Node\Scalar\String_ || $node->args[1]->value->value !== 'like') {
            return null;
        }

        $node->name = new Node\Identifier('whereLike');
        unset($node->args[1]);
        return $node;
    }
}
