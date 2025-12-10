<?php

declare(strict_types=1);

namespace RectorLaravel\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\NodeVisitorAbstract;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;

final class RandomEnumContextNodeVisitor extends NodeVisitorAbstract implements DecoratingNodeVisitorInterface
{
    public const string IS_IN_RANDOM_ENUM = 'is_in_random_enum';

    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver
    ) {}

    public function enterNode(Node $node)
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        // The randomEnum() method is a special case where the faker instance is used
        // see https://github.com/spatie/laravel-enum#faker-provider
        if ($this->nodeNameResolver->isName($node->name, 'randomEnum')) {
            $node->setAttribute(self::IS_IN_RANDOM_ENUM, true);
            SimpleCallableNodeTraverser::traverseNodesWithCallable($node, function (Node $subNode) {
                if (! $subNode instanceof PropertyFetch && ! $subNode instanceof InterpolatedString) {
                    return null;
                }

                $subNode->setAttribute(self::IS_IN_RANDOM_ENUM, true);

                return $subNode;
            });
        }

        return null;
    }
}
