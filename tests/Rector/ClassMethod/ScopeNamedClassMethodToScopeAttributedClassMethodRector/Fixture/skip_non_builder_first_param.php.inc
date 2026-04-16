<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonBuilderFirstParam extends Model
{
    public function scopeCategory(string $category): void
    {
    }
}

?>
