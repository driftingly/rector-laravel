<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\PostTag::class);
    }
}

?>
