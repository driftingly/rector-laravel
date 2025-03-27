<?php

namespace Illuminate\Contracts\Database\Eloquent;

use Illuminate\Contracts\Database\Query\Builder as BaseContract;

if (interface_exists('Illuminate\Contracts\Database\Eloquent\Builder')) {
    return;
}

/**
 * This interface is intentionally empty and exists to improve IDE support.
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
interface Builder extends BaseContract {}
