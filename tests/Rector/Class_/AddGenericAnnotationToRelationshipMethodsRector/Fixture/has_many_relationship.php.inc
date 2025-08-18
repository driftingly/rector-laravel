<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model {}

class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model {}

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture\Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

?>
