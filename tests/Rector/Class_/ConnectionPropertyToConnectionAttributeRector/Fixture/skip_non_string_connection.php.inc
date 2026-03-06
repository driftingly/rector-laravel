<?php

namespace RectorLaravel\Tests\Rector\Class_\ConnectionPropertyToConnectionAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonStringConnection extends Model
{
    protected $connection = ['sqlite'];
}

?>
