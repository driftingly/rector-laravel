<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Database\Eloquent\Builder $query */
$query->whereHas('posts', function ($query) {
    $query->where('is_published', true);
});

$query->orWhereHas('posts', function ($query) {
    $query->where('is_published', true);
});

$query->whereDoesntHave('posts', function ($query) {
    $query->where('is_published', true);
});

$query->orWhereDoesntHave('posts', function ($query) {
    $query->where('is_published', true);
});

$query->whereHasMorph('posts', function ($query) {
    $query->where('is_published', true);
});

$query->orWhereHasMorph('posts', function ($query) {
    $query->where('is_published', true);
});

$query->whereDoesntHaveMorph('posts', function ($query) {
    $query->where('is_published', true);
});

$query->orWhereDoesntHaveMorph('posts', function ($query) {
    $query->where('is_published', true);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Database\Eloquent\Builder $query */
$query->whereHas('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->orWhereHas('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->whereDoesntHave('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->orWhereDoesntHave('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->whereHasMorph('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->orWhereHasMorph('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->whereDoesntHaveMorph('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->orWhereDoesntHaveMorph('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

?>
