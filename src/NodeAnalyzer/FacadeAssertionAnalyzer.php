<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class FacadeAssertionAnalyzer
{
    /**
     * @readonly
     */
    private NodeTypeResolver $nodeTypeResolver;
    /**
     * @readonly
     */
    private NodeNameResolver $nodeNameResolver;
    public function __construct(NodeTypeResolver $nodeTypeResolver, NodeNameResolver $nodeNameResolver)
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
        $this->nodeNameResolver = $nodeNameResolver;
    }

    public function isFacadeAssertion(StaticCall $staticCall): bool
    {
        switch (true) {
            case $this->nodeTypeResolver->isObjectType($staticCall->class, new ObjectType('Illuminate\Support\Facades\Bus'))
            && $this->nodeNameResolver->isName($staticCall->name, 'assertDispatched'):
                return true;
            case $this->nodeTypeResolver->isObjectType($staticCall->class, new ObjectType('Illuminate\Support\Facades\Event'))
            && $this->nodeNameResolver->isName($staticCall->name, 'assertDispatched'):
                return true;
            default:
                return false;
        }
    }
}
