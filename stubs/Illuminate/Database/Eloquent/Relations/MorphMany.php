<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\MorphMany')) {
    return;
}

class MorphMany extends MorphOneOrMany {}
