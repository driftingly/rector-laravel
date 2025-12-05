<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SomeModel extends Model
{
    #[Scope]
    public function someScope($query) {}

    public function scopeFilterSomething($query) {}

    public function someGenericFunction($query) {}

    public function relationship(): HasMany
    {
        return $this->hasMany(Foo::class);
    }
}
