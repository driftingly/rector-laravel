<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class Photo extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'legacy_taggables');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class Photo extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\LegacyTaggable::class);
    }
}

?>
