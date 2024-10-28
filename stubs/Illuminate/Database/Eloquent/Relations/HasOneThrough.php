<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\HasOneThrough')) {
    return;
}

class HasOneThrough extends Relation
{
}
