<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::whereHas('posts', function ($query) {
    $query->where('is_published', true);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::whereHas('posts', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('is_published', true);
});

?>
