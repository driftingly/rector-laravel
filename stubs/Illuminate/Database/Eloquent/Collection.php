<?php

namespace Illuminate\Database\Eloquent;

if (class_exists('Illuminate\Database\Eloquent\Collection')) {
    return;
}

/**
 * Declares fresh() but no refresh(), unlike Model.
 */
class Collection
{
    /**
     * @param  array<int, string>|string  $with
     * @return static
     */
    public function fresh($with = [])
    {
        return $this;
    }
}
