<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

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

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', \RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source\Taggable::class);
    }
}

?>
