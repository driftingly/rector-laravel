<?php

namespace Illuminate\Contracts\Database\Query;

use Illuminate\Database\Grammar;

if (interface_exists('Illuminate\Contracts\Database\Query\Expression')) {
    return;
}

interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
