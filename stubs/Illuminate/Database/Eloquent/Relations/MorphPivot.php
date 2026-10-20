<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\MorphPivot')) {
    return;
}

class MorphPivot extends Pivot {}
