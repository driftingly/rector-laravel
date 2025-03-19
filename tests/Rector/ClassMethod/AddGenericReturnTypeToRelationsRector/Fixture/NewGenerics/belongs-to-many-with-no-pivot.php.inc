<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Website extends Model {}

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics\Website, $this>
     */
    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Website extends Model {}

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\NewGenerics\Website, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class);
    }
}

?>
