<?php

namespace RectorLaravel\Tests\Rector\Class_\ConnectionPropertyToConnectionAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipPublicConnection extends Model
{
    public $connection = 'sqlite';
}

?>
