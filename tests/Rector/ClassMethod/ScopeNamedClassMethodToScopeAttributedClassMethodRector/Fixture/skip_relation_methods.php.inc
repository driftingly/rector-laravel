<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkipRelationMethods extends Model
{
    public function scopeTypes(): HasMany
    {
        return $this->hasMany(ScopeType::class);
    }
}

?>
