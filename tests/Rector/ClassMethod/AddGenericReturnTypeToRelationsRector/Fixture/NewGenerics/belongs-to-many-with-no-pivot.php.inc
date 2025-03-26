<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\SomeModel;

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\SomeModel, $this>
     */
    public function models(): BelongsToMany
    {
        return $this->belongsToMany(SomeModel::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\SomeModel;

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\SomeModel, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function models(): BelongsToMany
    {
        return $this->belongsToMany(SomeModel::class);
    }
}

?>
