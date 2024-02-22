<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SomeFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SomeFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}

?>
