<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\OldGenerics;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {}

class User extends Model
{
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\OldGenerics;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {}

class User extends Model
{
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}

?>
