<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}

?>
