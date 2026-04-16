<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TypedBuilderParameter extends Model
{
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TypedBuilderParameter extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query)
    {
        return $query->where('active', 1);
    }
}

?>
