<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final readonly class FacadeAssertionAnalyzer
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver,
    ) {}

    public function isFacadeAssertion(StaticCall $staticCall): bool
    {
        return match (true) {
            $this->nodeTypeResolver->isObjectType($staticCall->class, new ObjectType('Illuminate\Support\Facades\Bus'))
            && $this->nodeNameResolver->isName($staticCall->name, 'assertDispatched') => true,
            $this->nodeTypeResolver->isObjectType($staticCall->class, new ObjectType('Illuminate\Support\Facades\Event'))
            && $this->nodeNameResolver->isName($staticCall->name, 'assertDispatched') => true,
            default => false,
        };
    }
}
