<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class UserNonStringTable extends Model
{
    protected $table = ['users'];
}

?>
