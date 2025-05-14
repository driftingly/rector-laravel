<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use Illuminate\Container\Attributes\CurrentUser;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use RectorLaravel\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ClassMethod\AuthUserToCurrentUserRector\AuthUserToCurrentUserRectorTest
 */
final class AuthUserToCurrentUserRector extends AbstractRector implements ConfigurableRectorInterface
{
    private const string USER = 'user';

    /** @var class-string $userModel */
    private string $userModel;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes something',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
            ,
            ['option' => 'value']
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->userModel = $configuration['userModel'] ?? 'App\\Models\\User';
    }

    /**
     * @param  ClassMethod  $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;
        $this->traverseNodesWithCallable($node, function (Node $node) use (&$hasChanged) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            if (! $this->isName($node->name, 'user')) {
                return null;
            }

            if (! $node->var instanceof FuncCall) {
                return null;
            }

            if (! $this->isName($node->var->name, 'auth')) {
                return null;
            }

            $hasChanged = true;

            return new Variable(self::USER);
        });

        if ($hasChanged) {
            $node->params[] = new Param(
                new Variable(self::USER),
                type: new FullyQualified($this->userModel),
                attrGroups: [
                    new AttributeGroup([
                        new Attribute(new FullyQualified(CurrentUser::class)),
                    ]),
                ],
            );
        }

        return $hasChanged
            ? $node
            : null;
    }
}
