<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Comment;

class SkipMismatchedTable extends Model
{
    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_user');
    }
}
