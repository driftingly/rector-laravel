<?php

namespace RectorLaravel\Tests\Rector\Class_\FillablePropertyToFillableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonStringFillable extends Model
{
    protected $fillable = [
        'name',
        123,
    ];
}

?>
