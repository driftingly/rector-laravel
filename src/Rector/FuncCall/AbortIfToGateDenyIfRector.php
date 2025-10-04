<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\AbortIfToGateDenyIfRectorTest
 */
final class AbortIfToGateDenyIfRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change abort_if() to Gate::denyIf() when condition is user-related', [
            new CodeSample(
                <<<'CODE_SAMPLE'
abort_if($user->id === $post->user_id);
abort_if($post->user()->is($user));
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Gate::denyIf($user->id === $post->user_id);
\Illuminate\Support\Facades\Gate::denyIf($post->user()->is($user));
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'abort_if')) {
            return null;
        }

        if (! isset($node->args[0])) {
            return null;
        }

        $condition = $node->args[0]->value;

        if (! $this->containsUserRelatedCheck($condition)) {
            return null;
        }

        return new StaticCall(
            new FullyQualified('Illuminate\Support\Facades\Gate'),
            'denyIf',
            $node->args
        );
    }

    /**
     * Check if the expression contains user-related references
     */
    private function containsUserRelatedCheck(Node $node): bool
    {
        $hasUserReference = false;

        $this->traverseNodesWithCallable($node, function (Node $subNode) use (&$hasUserReference): ?int {
            if ($subNode instanceof Variable && is_string($subNode->name)) {
                if (str_contains(strtolower($subNode->name), 'user')) {
                    $hasUserReference = true;
                    return null;
                }
            }

            if ($subNode instanceof PropertyFetch) {
                $propertyName = $this->getName($subNode->name);
                if ($propertyName && str_contains(strtolower($propertyName), 'user')) {
                    $hasUserReference = true;
                    return null;
                }
            }

            if ($subNode instanceof FuncCall && $this->isName($subNode->name, 'auth')) {
                $hasUserReference = true;
                return null;
            }

            if ($subNode instanceof StaticCall && $this->isName($subNode->class, 'Auth')) {
                $hasUserReference = true;
                return null;
            }

            return null;
        });

        return $hasUserReference;
    }
}
