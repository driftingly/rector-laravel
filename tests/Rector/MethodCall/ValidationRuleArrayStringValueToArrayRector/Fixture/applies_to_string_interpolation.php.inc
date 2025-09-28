<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $usersTable = 'users';
    $required = 'required';
    $size = 500;

    $validator->make(['user_id' => 1], [
        'user_id' => "required|exists:{$usersTable},id|integer",
        'user_other_id' => "{$required}|exists:{$usersTable},id|{$required}",
        'layout' => "required|mimes:png|dimensions:width={$size},height={$size}",
        'layouts' => "required|mimes:png|dimensions:width={$validator->size},height={$validator->size}",
    ]);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $usersTable = 'users';
    $required = 'required';
    $size = 500;

    $validator->make(['user_id' => 1], [
        'user_id' => ["required", "exists:{$usersTable},id", "integer"],
        'user_other_id' => ["{$required}", "exists:{$usersTable},id", "{$required}"],
        'layout' => ["required", "mimes:png", "dimensions:width={$size},height={$size}"],
        'layouts' => ["required", "mimes:png", "dimensions:width={$validator->size},height={$validator->size}"],
    ]);
}

?>
