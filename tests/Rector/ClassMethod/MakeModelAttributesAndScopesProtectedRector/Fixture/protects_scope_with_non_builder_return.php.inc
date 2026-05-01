<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProtectsScopeWithNonBuilderReturn extends Model
{
    public function scopeString(Builder $query): string
    {
        return 'foo';
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProtectsScopeWithNonBuilderReturn extends Model
{
    protected function scopeString(Builder $query): string
    {
        return 'foo';
    }
}

?>
