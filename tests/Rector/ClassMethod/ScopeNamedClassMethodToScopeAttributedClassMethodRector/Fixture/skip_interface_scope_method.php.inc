<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Source\Publishable;

class SkipInterfaceScopeMethod extends Model implements Publishable
{
    public function scopeArchived(Builder $query): void
    {
        $query->whereNotNull('archived_at');
    }
}

?>
