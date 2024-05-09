<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->where('column', function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->where('column', '>', function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->orWhere(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->whereNot(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->whereExists(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->where('column', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->where('column', '>', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->orWhere(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->whereNot(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

$query->whereExists(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
