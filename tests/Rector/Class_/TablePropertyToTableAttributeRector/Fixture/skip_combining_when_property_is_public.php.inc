<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'users')]
class SkipCombiningWhenPropertyIsPublic extends Model
{
    public $primaryKey = 'user_id';
}

?>
