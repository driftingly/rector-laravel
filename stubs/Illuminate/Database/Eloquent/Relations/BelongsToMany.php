<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\BelongsToMany')) {
    return;
}

class BelongsToMany extends Relation {}
