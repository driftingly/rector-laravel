<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\BelongsTo')) {
    return;
}

class BelongsTo extends Relation
{
}
