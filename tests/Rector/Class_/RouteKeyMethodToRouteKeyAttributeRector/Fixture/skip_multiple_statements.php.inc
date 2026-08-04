<?php

namespace RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class PostWithMultipleStatements extends Model
{
    public function getRouteKeyName(): string
    {
        if ($this->slug) {
            return $this->slug;
        }

        return 'id';
    }
}

?>
