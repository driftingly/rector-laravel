<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class UserPublicTable extends Model
{
    public $table = 'users';
}

?>
