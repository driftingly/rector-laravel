<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

trait TaggedByConvention
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
}

class TaggedPost extends Model
{
    use TaggedByConvention;
}
