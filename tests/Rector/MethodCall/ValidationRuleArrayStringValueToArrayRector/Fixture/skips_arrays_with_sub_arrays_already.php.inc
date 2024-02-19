<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function skip_arrays_with_sub_arrays_already(\Illuminate\Validation\Factory $validator) {
    $validator->make(['name' => 'value'], [
        'name' => ['required', 'string'],
    ]);
}

?>
