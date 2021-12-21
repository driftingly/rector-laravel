<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\HasMany')) {
    return;
}

class HasMany extends Relation
{
}
