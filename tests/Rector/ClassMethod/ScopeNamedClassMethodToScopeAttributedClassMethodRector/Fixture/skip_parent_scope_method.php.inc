<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use RectorLaravel\Tests\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Source\ParentPost;

class SkipParentScopeMethod extends ParentPost
{
    public function scopeArchived(Builder $query): void
    {
        $query->whereNotNull('archived_at');
    }
}

?>
