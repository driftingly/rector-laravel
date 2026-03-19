<?php

declare(strict_types=1);

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;

final class TableAttributeFactory
{
    /**
     * @param  array<string, Expr>  $options
     */
    public function create(Expr $table, array $options): AttributeGroup
    {
        $args = [new Arg($table, false, false, [], new Identifier('table'))];

        foreach ($options as $name => $expr) {
            $args[] = new Arg($expr, false, false, [], new Identifier($name));
        }

        return new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\Table'),
                $args
            ),
        ]);
    }
}
