<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipClassWhichIsNotAModel extends Model
{
    public function files()
    {
        return $this->morphToMany(File::class, 'attachable', 'attachment');
    }
}
