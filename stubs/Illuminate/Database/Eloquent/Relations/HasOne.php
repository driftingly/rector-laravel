<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\HasOne')) {
    return;
}

class HasOne extends HasOneOrMany {}
