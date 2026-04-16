<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScopeWithExtraParameters extends Model
{
    public function scopeOfType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScopeWithExtraParameters extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function ofType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }
}

?>
