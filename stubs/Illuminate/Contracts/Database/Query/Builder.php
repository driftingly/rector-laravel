<?php

namespace Illuminate\Contracts\Database\Query;

if (interface_exists('Illuminate\Contracts\Database\Query\Builder')) {
    return;
}

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
interface Builder {}
