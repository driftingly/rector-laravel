<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    protected $hidden = [
        'password',
    ];

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    protected $incrementing = false;

    protected $appends = [
        'full_name',
    ];

    protected $guarded = [
        'is_admin',
    ];

    protected $touches = [
        'posts',
    ];

    protected $connection = 'sqlite';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Appends([
    'full_name',
])]
#[\Illuminate\Database\Eloquent\Attributes\Connection('sqlite')]
#[\Illuminate\Database\Eloquent\Attributes\Fillable([
    'name',
    'email',
])]
#[\Illuminate\Database\Eloquent\Attributes\Guarded([
    'is_admin',
])]
#[\Illuminate\Database\Eloquent\Attributes\Hidden([
    'password',
])]
#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'users', key: 'user_id', keyType: 'string', incrementing: false)]
#[\Illuminate\Database\Eloquent\Attributes\Touches([
    'posts',
])]
class User extends Model
{
}

?>
