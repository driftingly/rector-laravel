<?php

namespace Rector\Laravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $casts = [
        'age' => 'integer',
    ];

    protected $dates = ['birthday', 'created_at'];
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $casts = [
        'age' => 'integer', 'birthday' => 'datetime', 'created_at' => 'datetime',
    ];
}

?>
