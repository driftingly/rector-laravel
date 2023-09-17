<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

/** @var \Illuminate\Database\Eloquent\Builder $query */
User::whereHas('posts', function ($query) {
    $query->where('is_published', true);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

/** @var \Illuminate\Database\Eloquent\Builder $query */
User::whereHas('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

?>
