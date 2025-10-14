<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkipScopeMethodsNotScope extends Model
{
    public function scope(): string
    {
        return 'foo';
    }

    public function scopedItems(): HasMany
    {
        return $this->hasMany(ScopedItem::class);
    }

    public function scopeQuery(): Builder
    {
        return $this->where('foo', true);
    }

    public function scopeString(Builder $builder): string
    {
        return 'foo';
    }

    public function scopeVoid(): void {}

    public function scopeNoReturn(Builder $builder) {}
}

?>
