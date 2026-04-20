<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NonBuilderReturnType extends Model
{
    public function scopeString(Builder $query): string
    {
        return 'foo';
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NonBuilderReturnType extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function string(Builder $query): string
    {
        return 'foo';
    }
}

?>
