<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonDuplicateAttributeNodes extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function scopeSomeMethod()
    {

    }
}

?>
