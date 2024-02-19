<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    \Illuminate\Support\Facades\Validator::make(['name' => 'value'], [
        'name' => 'required|string',
    ]);

    $validator->make(['name' => 'value'], [
        'name' => 'required|string',
    ]);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    \Illuminate\Support\Facades\Validator::make(['name' => 'value'], [
        'name' => ['required', 'string'],
    ]);

    $validator->make(['name' => 'value'], [
        'name' => ['required', 'string'],
    ]);
}

?>
