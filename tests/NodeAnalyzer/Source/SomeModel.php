<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    #[Scope]
    public function someScope($query)
    {

    }

    public function scopeFilterSomething($query)
    {

    }

    public function someGenericFunction($query)
    {

    }
}
