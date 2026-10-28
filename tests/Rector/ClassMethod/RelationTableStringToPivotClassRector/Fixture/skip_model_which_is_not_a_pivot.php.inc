<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class SkipModelWhichIsNotAPivot extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'taggables');
    }
}
