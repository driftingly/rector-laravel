<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

class SkipCallOnNonModel
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    private function belongsToMany($related, $table)
    {
        return [$related, $table];
    }
}
