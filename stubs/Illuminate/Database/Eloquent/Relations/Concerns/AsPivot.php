<?php

namespace Illuminate\Database\Eloquent\Relations\Concerns;

if (trait_exists('Illuminate\Database\Eloquent\Relations\Concerns\AsPivot')) {
    return null;
}

/**
 * The framework recognises a pivot model by the use of this trait, so only the name of it matters here.
 */
trait AsPivot {}
