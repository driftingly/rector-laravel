<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\User;

class SkipRelatedModelAsTable extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }
}
