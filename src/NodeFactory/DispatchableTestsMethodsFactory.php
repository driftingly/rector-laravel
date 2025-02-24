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
     * @param  list<String_|ClassConstFetch>  $items
     * @param  class-string  $facade
     */
    public function makeFacadeFakeCall(array $items, string $facade): StaticCall
    {
        return new StaticCall(
            new FullyQualified($facade),
            'fake',
            [new Arg(new Array_(array_map(fn (String_|ClassConstFetch $item): ArrayItem => new ArrayItem($item), $items)))]
        );
    }

    /**
     * @param  list<String_|ClassConstFetch>  $items
     * @return StaticCall[]
     */
    public function assertStatements(array $items, string $facade): array
    {
        return array_map(fn (String_|ClassConstFetch $item): Expression => new Expression(new StaticCall(
            new FullyQualified($facade),
            'assertDispatched',
            [new Arg($item)],
        )), $items);
    }

    /**
     * @param  list<String_|ClassConstFetch>  $items
     * @return StaticCall[]
     */
    public function assertNotStatements(array $items, string $facade): array
    {
        return array_map(fn (String_|ClassConstFetch $item): Expression => new Expression(new StaticCall(
            new FullyQualified($facade),
            'assertNotDispatched',
            [new Arg($item)],
        )), $items);
    }
}
