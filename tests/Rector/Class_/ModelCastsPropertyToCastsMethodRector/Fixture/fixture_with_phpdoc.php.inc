<?php

namespace RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class CastsPropertyExists extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthday' => 'datetime',
            'age' => 'integer',
        ];
    }
}

?>
