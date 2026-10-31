<?php

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

if (class_exists('Illuminate\Database\Eloquent\Relations\Pivot')) {
    return;
}

class Pivot extends Model
{
    use AsPivot;
}
