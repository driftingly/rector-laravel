<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FooBar extends Pivot
{
    protected $table = 'foo_bar';
}
