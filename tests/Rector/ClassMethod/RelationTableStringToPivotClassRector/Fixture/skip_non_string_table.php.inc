<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Role;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Tag;

class SkipNonStringTable extends Model
{
    public function tags($table)
    {
        return $this->belongsToMany(Tag::class, $table);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
