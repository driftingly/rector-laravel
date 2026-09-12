<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class Video extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class Video extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Taggable::class);
    }
}

?>
