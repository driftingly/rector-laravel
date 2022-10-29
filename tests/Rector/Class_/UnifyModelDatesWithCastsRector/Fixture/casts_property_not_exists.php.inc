<?php

namespace RectorLaravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class CastsPropertyNotExists extends Model
{
    protected $dates = ['birthday'];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class CastsPropertyNotExists extends Model
{
    protected $casts = ['birthday' => 'datetime'];
}

?>
