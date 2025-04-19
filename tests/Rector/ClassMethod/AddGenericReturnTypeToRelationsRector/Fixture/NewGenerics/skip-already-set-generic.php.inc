<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\SomeModel;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SkipAlreadySetGeneric extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<SomeModel, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(SomeModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SomeModel, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(SomeModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<SomeModel, $this, Pivot>
     */
    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(SomeModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<SomeModel, $this, Pivot>
     */
    public function photos(): MorphToMany
    {
        return $this->morphToMany(SomeModel::class);
    }
}
?>
