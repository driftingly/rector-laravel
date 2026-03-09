<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormRequestWithMixedRulesAndClosures extends FormRequest
{
    public function rules()
    {
        return [
            'items.*.value' => Rule::forEach(function () {
                return [
                    'nullable',
                    'string',
                ];
            }),
            'name' => 'required|string',
        ];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormRequestWithMixedRulesAndClosures extends FormRequest
{
    public function rules()
    {
        return [
            'items.*.value' => Rule::forEach(function () {
                return [
                    'nullable',
                    'string',
                ];
            }),
            'name' => ['required', 'string'],
        ];
    }
}

?>
