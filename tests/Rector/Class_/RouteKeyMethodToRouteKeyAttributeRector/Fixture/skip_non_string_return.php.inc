<?php

namespace RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class PostWithVariableReturn extends Model
{
    public function getRouteKeyName(): string
    {
        return $this->routeKey;
    }
}

?>
