<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Product extends Model
{
    use HasFactory;

    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(Variation::class);
    }

    public function pieces(): BelongsToMany
    {
        return $this->belongsToMany(Variation::class);
    }

    public function photo(): HasOne
    {
        return $this->hasOne(Photo::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }

    public function labels(): MorphToMany
    {
        return $this->morphToMany(Label::class, 'labelable'); // LOL
    }
}
