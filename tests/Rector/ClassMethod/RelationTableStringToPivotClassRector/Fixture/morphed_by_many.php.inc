<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Video;

class Tag extends Model
{
    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable', 'taggables');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Video;

class Tag extends Model
{
    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable', \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Taggable::class);
    }
}

?>
