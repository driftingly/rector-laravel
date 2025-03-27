<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonMatchingMethods extends Model
{
    public function scopefoo()
    {

    }
}

?>
