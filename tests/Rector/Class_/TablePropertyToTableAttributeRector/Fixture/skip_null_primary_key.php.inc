<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users', timestamps: false)]
class SkipNullPrimaryKey extends Model
{
    protected $primaryKey = null;
}

?>
