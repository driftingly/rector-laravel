<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variation extends Model
{
    use HasFactory;

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }
}
