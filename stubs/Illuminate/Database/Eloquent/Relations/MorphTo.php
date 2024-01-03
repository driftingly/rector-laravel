<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\MorphTo')) {
    return;
}

class MorphTo extends BelongsTo
{
}
