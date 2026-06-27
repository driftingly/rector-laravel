<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\MorphToMany')) {
    return;
}

class MorphToMany extends BelongsToMany {}
