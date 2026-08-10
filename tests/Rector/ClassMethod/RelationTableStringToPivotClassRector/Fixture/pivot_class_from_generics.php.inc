<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Comment;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser;

class GenericsPivotClass extends Model
{
    /**
     * @return BelongsToMany<Comment, $this, CommentUser>
     */
    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'legacy_comment_user');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Comment;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser;

class GenericsPivotClass extends Model
{
    /**
     * @return BelongsToMany<Comment, $this, CommentUser>
     */
    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser::class);
    }
}

?>
