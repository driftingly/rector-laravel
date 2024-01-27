<?php

namespace RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\Fixture;

class NonModelClass
{
    protected $casts = [
        'birthday' => 'datetime',
        'age' => 'integer',
    ];
}

?>
