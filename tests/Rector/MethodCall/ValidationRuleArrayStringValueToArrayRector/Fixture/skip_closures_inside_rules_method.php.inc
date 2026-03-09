<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormRequestWithClosureInRules extends FormRequest
{
    public function rules()
    {
        return [
            'field' => Rule::forEach(function () {
                return [
                    'nullable',
                    Rule::in(['a', 'b']),
                ];
            }),
        ];
    }
}

?>
