<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Contracts\Validation\Factory;

\Illuminate\Support\Facades\Validator::make([], [
    'key' => [function ($attribute, $value, $fail) {
        $fail('The '.$attribute.' is invalid.');
    }],
]);

/** @var Factory $factory */
$factory->make([], [
    'key' => [function ($attribute, $value, $fail) {
        $fail('The '.$attribute.' is invalid.');
    }],
]);

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Contracts\Validation\Factory;

\Illuminate\Support\Facades\Validator::make([], [
    'key' => [function (string $attribute, mixed $value, \Closure $fail) {
        $fail('The '.$attribute.' is invalid.');
    }],
]);

/** @var Factory $factory */
$factory->make([], [
    'key' => [function (string $attribute, mixed $value, \Closure $fail) {
        $fail('The '.$attribute.' is invalid.');
    }],
]);

?>
