<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Validation\Rule;

Rule::forEach(function ($value, $attribute) {
});

/** @var \Illuminate\Validation\Rules\Exists $exists */
$exists->where(function ($query) {
    $query->where('column', 'value');
});

/** @var \Illuminate\Validation\Rules\Exists $exists */
$exists->where('column', function ($query) {
    $query->where('column', 'value');
});

$exists->using(function ($query) {
    $query->where('column', 'value');
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Validation\Rule;

Rule::forEach(function (mixed $value, string $attribute) {
});

/** @var \Illuminate\Validation\Rules\Exists $exists */
$exists->where(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('column', 'value');
});

/** @var \Illuminate\Validation\Rules\Exists $exists */
$exists->where('column', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('column', 'value');
});

$exists->using(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('column', 'value');
});

?>
