<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\AssertSeeToAssertSeeHtmlRector\AssertSeeToAssertSeeHtmlRectorTest
 */
final class AssertSeeToAssertSeeHtmlRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @var string[]
     */
    protected $methodsToReplace = [
        'assertSee' => 'assertSeeHtml',
        'assertDontSee' => 'assertDontSeeHtml',
        'assertSeeInOrder' => 'assertSeeHtmlInOrder',
    ];

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace assertSee with assertSeeHtml when testing HTML with escape set to false',
            [
                new CodeSample(
                    '$response->assertSee("<li>foo</li>", false);',
                    '$response->assertSeeHtml("<li>foo</li>");'
                ),
                new CodeSample(
                    '$response->assertDontSee("<li>foo</li>", false);',
                    '$response->assertDontSeeHtml("<li>foo</li>");'
                ),
                new CodeSample(
                    '$response->assertSeeInOrder(["<li>foo</li>", "<li>bar</li>"], false);',
                    '$response->assertSeeHtmlInOrder(["<li>foo</li>", "<li>bar</li>"]);'
                ),
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
        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Testing\TestResponse'))) {
            return null;
        }

        $methodCallName = (string) $this->getName($node->name);

        if (! array_key_exists($methodCallName, $this->methodsToReplace)) {
            return null;
        }

        if (count($node->getArgs()) !== 2) {
            return null;
        }

        if (! $this->valueResolver->isFalse($node->getArgs()[1]->value)) {
            return null;
        }

        return $this->nodeFactory->createMethodCall(
            $node->var,
            $this->methodsToReplace[$methodCallName],
            [$node->getArgs()[0]]
        );
    }
}
