<?php

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;

class DispatchableTestsMethodsFactory
{
    /**
     * @param  array<int<0, max>, ClassConstFetch|String_>  $items
     */
    public function makeFacadeFakeCall(array $items, string $facade): StaticCall
    {
        return new StaticCall(
            new FullyQualified($facade),
            'fake',
            [new Arg(new Array_(array_map(fn ($item): ArrayItem => new ArrayItem($item), $items)))]
        );
    }

    /**
     * @param  array<int<0, max>, ClassConstFetch|String_>  $items
     * @return Expression[]
     */
    public function assertStatements(array $items, string $facade): array
    {
        return array_map(fn ($item): Expression => new Expression(new StaticCall(
            new FullyQualified($facade),
            'assertDispatched',
            [new Arg($item)],
        )), $items);
    }

    /**
     * @param  array<int<0, max>, ClassConstFetch|String_>  $items
     * @return Expression[]
     */
    public function assertNotStatements(array $items, string $facade): array
    {
        return array_map(fn ($item): Expression => new Expression(new StaticCall(
            new FullyQualified($facade),
            'assertNotDispatched',
            [new Arg($item)],
        )), $items);
    }
}
