<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where(...);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Product> $builder
     * @return \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Product>
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where(...);
    }
}

?>
