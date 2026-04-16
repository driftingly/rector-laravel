<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SkipNonScopeReturnType extends Model
{
    public function scopeString(Builder $query): string
    {
        return 'foo';
    }
}

?>
