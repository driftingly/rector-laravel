<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Contracts\Validation\Validator;

/** @var Validator $validator */
$validator->after(function ($validator) {
    if ($this->somethingElseIsInvalid()) {
        $validator->errors()->add(
            'field', 'Something is wrong with this field!'
        );
    }
});

$validator->sometimes('reason', 'required|max:500', function ($input, $item) {
    return $input->games >= 100;
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Validation\Fixture;

use Illuminate\Contracts\Validation\Validator;

/** @var Validator $validator */
$validator->after(function (\Illuminate\Contracts\Validation\Validator $validator) {
    if ($this->somethingElseIsInvalid()) {
        $validator->errors()->add(
            'field', 'Something is wrong with this field!'
        );
    }
});

$validator->sometimes('reason', 'required|max:500', function (\Illuminate\Support\Fluent $input, mixed $item) {
    return $input->games >= 100;
});

?>
