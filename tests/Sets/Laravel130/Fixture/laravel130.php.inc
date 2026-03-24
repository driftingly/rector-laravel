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

final class ProcessPodcast implements \Illuminate\Contracts\Queue\ShouldQueue
{
    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 20, 30];
    public $queue = 'podcasts';
    public $connection = 'redis';
    public $failOnTimeout = true;
    public $maxExceptions = 3;
    public $uniqueFor = 600;
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
#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users', key: 'user_id', keyType: 'string', incrementing: false)]
#[\Illuminate\Database\Eloquent\Attributes\Touches([
    'posts',
])]
class User extends Model
{
}

#[\Illuminate\Queue\Attributes\Backoff([10, 20, 30])]
#[\Illuminate\Queue\Attributes\FailOnTimeout(true)]
#[\Illuminate\Queue\Attributes\Connection('redis')]
#[\Illuminate\Queue\Attributes\MaxExceptions(3)]
#[\Illuminate\Queue\Attributes\Queue('podcasts')]
#[\Illuminate\Queue\Attributes\Timeout(120)]
#[\Illuminate\Queue\Attributes\Tries(3)]
#[\Illuminate\Queue\Attributes\UniqueFor(600)]
final class ProcessPodcast implements \Illuminate\Contracts\Queue\ShouldQueue
{
}

?>
