<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable', 'taggables');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable', \RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source\Taggable::class);
    }
}

?>
