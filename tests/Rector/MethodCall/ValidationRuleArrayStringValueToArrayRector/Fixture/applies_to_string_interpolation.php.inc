<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $usersTable = 'users';
    $required = 'required';

    $validator->make(['user_id' => 1], [
        'user_id' => "required|exists:{$usersTable},id|integer",
        'user_other_id' => "{$required}|exists:{$usersTable},id|{$required}",
    ]);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $usersTable = 'users';
    $required = 'required';

    $validator->make(['user_id' => 1], [
        'user_id' => ["required", "exists:{$usersTable},id", "integer"],
        'user_other_id' => ["{$required}", "exists:{$usersTable},id", "{$required}"],
    ]);
}

?>
