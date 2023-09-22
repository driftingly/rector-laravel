<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereHas('posts');
$query->whereHas('posts', null);
$query->whereHasMorph('posts', '', null);

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::whereHas('posts');
User::whereHas('posts', null);
User::whereHasMorph('posts', '');
User::whereHasMorph('posts', '', null);

?>
