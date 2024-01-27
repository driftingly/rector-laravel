<?php

namespace RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class ClassWithCastsMethod extends Model
{
    protected $casts = [
        'name' => 'string',
    ];

    private function casts(): array
    {
        return [
            'birthday' => 'datetime',
            'age' => 'integer',
        ];
    }
}

?>
