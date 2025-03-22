<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeClass extends Model
{
    public function scopeSomeMethod()
    {

    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeClass extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    public function someMethod()
    {

    }
}

?>
