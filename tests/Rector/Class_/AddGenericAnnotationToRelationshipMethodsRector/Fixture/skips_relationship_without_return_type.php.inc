<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model {}

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model {}

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

?>
