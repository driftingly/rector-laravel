<?php

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Expression;

class DispatchableTestsMethodsFactory
{
    public function makeFacadeFakeCall(array $classes, string $facade): StaticCall
    {
        return new StaticCall(
            new FullyQualified($facade),
            'fake',
            [new Arg (new Array_(array_map(function (string $class): ArrayItem {
                return new ArrayItem(new ClassConstFetch(new FullyQualified($class), 'class'));
            }, $classes)))]
        );
    }

    /**
     * @param class-string[] $classes
     * @return StaticCall[]
     */
    public function assertStatements(array $classes, string $facade): array
    {
        return array_map(function (string $class) use ($facade): Expression {
            return new Expression(new StaticCall(
                new FullyQualified($facade),
                'assertDispatched',
                [new Arg(new ClassConstFetch(new FullyQualified($class), 'class'))],
            ));
        }, $classes);
    }

    /**
     * @param class-string[] $classes
     * @return StaticCall[]
     */
    public function assertNotStatements(array $classes, string $facade): array
    {
        return array_map(function (string $class) use ($facade): Expression {
            return new Expression(new StaticCall(
                new FullyQualified($facade),
                'assertNotDispatched',
                [new Arg(new ClassConstFetch(new FullyQualified($class), 'class'))],
            ));
        }, $classes);
    }
}
