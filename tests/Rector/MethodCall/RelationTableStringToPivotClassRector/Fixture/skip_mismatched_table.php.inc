<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipMismatchedTable extends Model
{
    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_user');
    }
}
