<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model {}

class User extends Model
{
    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model {}

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\Account, \RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\User>
     */
    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

?>
