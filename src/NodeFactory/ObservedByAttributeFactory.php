<?php

declare(strict_types=1);

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;

final class ObservedByAttributeFactory
{
    /**
     * @param  list<string>  $observerClasses
     */
    public function create(array $observerClasses): AttributeGroup
    {
        $items = [];

        foreach ($observerClasses as $observerClass) {
            $items[] = new ArrayItem(new ClassConstFetch(new FullyQualified($observerClass), 'class'));
        }

        return new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\\Database\\Eloquent\\Attributes\\ObservedBy'),
                [new Arg(new Array_($items))]
            ),
        ]);
    }
}
