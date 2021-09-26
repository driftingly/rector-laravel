<?php

namespace Rector\Laravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class DatesExistsInCasts extends Model
{
    protected $casts = [
        'birthday' => 'date:Y-m',
    ];

    protected $dates = ['birthday'];
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class DatesExistsInCasts extends Model
{
    protected $casts = [
        'birthday' => 'date:Y-m',
    ];
}

?>
