<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Source;

use Illuminate\Database\Eloquent\Builder;

interface Publishable
{
    public function scopeArchived(Builder $query): void;
}
