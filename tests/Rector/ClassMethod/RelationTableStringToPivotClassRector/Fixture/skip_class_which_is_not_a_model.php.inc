<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\File;

class SkipClassWhichIsNotAModel extends Model
{
    public function files()
    {
        return $this->morphToMany(File::class, 'attachable', 'attachment');
    }
}
