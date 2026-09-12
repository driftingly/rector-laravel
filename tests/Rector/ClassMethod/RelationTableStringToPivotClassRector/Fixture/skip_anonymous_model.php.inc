<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

$model = new class extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'legacy_taggables');
    }
};
