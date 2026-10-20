<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Source;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ParentPost extends Model
{
    public function scopeArchived(Builder $query): void
    {
        $query->whereNotNull('archived_at');
    }
}
