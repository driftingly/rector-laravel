<?php

namespace RectorLaravel\Tests\Rector\Class_\FillablePropertyToFillableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class PublicFillable extends Model
{
    public $fillable = ['name'];
}

?>
