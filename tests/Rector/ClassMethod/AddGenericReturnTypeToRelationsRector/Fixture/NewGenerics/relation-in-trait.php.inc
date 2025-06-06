<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

trait AccountTrait
{
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}

class User extends Model
{
    use AccountTrait;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

trait AccountTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics\Account, $this>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}

class User extends Model
{
    use AccountTrait;
}

?>
