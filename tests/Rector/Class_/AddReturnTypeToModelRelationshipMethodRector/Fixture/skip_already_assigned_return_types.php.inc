<?php

namespace RectorLaravel\Tests\Rector\Class_\AddReturnTypeToModelRelationshipMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipAlreadyAssigned extends Model
{
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Posts::class);
    }
}

?>
