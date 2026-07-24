<?php

namespace RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\Fixture;

class NotAModel
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

?>
