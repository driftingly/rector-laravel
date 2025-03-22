<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonDuplicateMethod extends Model
{
    public function scopeSomeMethod()
    {

    }

    public function someMethod()
    {

    }
}

?>
