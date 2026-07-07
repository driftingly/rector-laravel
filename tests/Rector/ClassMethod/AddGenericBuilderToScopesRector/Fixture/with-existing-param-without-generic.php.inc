<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $this->query->where(...);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Account> $query
     * @return \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Account>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $this->query->where(...);
    }
}

?>
