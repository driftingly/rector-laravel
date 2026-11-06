<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function everything()
    {
        return static::query()->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function everything()
    {
        return static::all();
    }
}

?>
