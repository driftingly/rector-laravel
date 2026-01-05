<?php

namespace RectorLaravel\Tests\Rector\Class_\AddReturnTypeToModelRelationshipMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonModel
{
    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
}

?>
