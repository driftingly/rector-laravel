<?php

namespace RectorLaravel\Tests\Rector\Class_\AddReturnTypeToModelRelationshipMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmbeddedFunctionLikeUse extends Model
{
    public function posts()
    {
        return function () {
            return $this->hasMany(Posts::class);
        };
    }
}

?>
