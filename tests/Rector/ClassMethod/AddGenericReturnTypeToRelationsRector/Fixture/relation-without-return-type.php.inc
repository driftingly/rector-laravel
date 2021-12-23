<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

class User extends Model
{
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}

?>
