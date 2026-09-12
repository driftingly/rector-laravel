<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Comment;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser;

class UsingPivotClass extends Model
{
    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'legacy_comment_user')->using(CommentUser::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Comment;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser;

class UsingPivotClass extends Model
{
    public function comments()
    {
        return $this->belongsToMany(Comment::class, \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser::class)->using(CommentUser::class);
    }
}

?>
