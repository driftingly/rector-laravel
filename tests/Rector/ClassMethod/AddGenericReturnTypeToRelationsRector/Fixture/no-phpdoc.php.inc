<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

class User extends Model
{
    public function accounts(): HasMany
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
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Rector\Laravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\Account>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}

?>
