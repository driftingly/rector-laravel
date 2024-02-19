<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

function skip_non_validator_method_call($validator) {
    $validator->make(['name' => 'value'], [
        'name' => ['required', 'string'],
    ]);
}

?>
