<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Fixture\OldGenerics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class User extends Model
{
    /**
     * @return MorphTo<Model,  User>
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo('translatable', 'model', 'value');
    }
}
