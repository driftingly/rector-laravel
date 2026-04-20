<?php

namespace RectorLaravel\Tests\Sets\Laravel130WithoutAttributes\Fixture;

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

namespace RectorLaravel\Tests\Sets\Laravel130WithoutAttributes\Fixture;

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
