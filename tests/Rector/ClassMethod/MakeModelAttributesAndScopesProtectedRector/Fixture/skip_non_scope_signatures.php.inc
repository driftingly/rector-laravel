<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkipNonScopeSignatures extends Model
{
    public function scopeParam(string $param): Builder
    {
        return $this->where('foo', true);
    }

    public function scopeVoid(): void {}

    public function scopeItem(): BelongsTo
    {
        return $this->belongsTo(ScopeItem::class);
    }
}

?>
