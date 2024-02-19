<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $validator->make(['name' => 'value'], [
        'name' => 'required|string',
    ]);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function something(\Illuminate\Validation\Factory $validator) {
    $validator->make(['name' => 'value'], [
        'name' => ['required', 'string'],
    ]);
}

?>
