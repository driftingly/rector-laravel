<?php

namespace RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\RouteKey('slug')]
class Post extends Model
{
}

?>
