<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::with(['team' => function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}]);

User::withCount(['team' => function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}]);

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::with(['team' => function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}]);

User::withCount(['team' => function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}]);

?>
