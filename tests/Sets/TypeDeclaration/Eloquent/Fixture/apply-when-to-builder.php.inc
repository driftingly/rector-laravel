<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->when(true, function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->when(true, function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}, function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->when(true, function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->when(true, function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
}, function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
