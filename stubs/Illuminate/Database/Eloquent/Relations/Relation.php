<?php

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Contracts\Database\Eloquent\Builder;

if (class_exists('Illuminate\Database\Eloquent\Relations\Relation')) {
    return;
}

abstract class Relation implements Builder {}
