<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipFormRequestNonRulesClassMethod extends FormRequest
{
    public function someMethod()
    {
        return [
            'name' => 'required|string',
        ];
    }
}

?>
