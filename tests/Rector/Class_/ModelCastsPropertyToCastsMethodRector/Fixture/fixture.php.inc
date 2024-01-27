<?php

namespace RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class CastsPropertyExists extends Model
{
    protected $casts = [
        'birthday' => 'datetime',
        'age' => 'integer',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class CastsPropertyExists extends Model
{
    protected function casts(): array
    {
        return [
            'birthday' => 'datetime',
            'age' => 'integer',
        ];
    }
}

?>
