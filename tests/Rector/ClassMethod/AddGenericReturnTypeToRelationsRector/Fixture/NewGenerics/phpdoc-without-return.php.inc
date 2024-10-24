<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

class User extends Model
{
    /**
     * This is a dummy comment about this method.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {}

class User extends Model
{
    /**
     * This is a dummy comment about this method.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics\Account, $this>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}

?>
