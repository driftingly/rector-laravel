<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

class SkipNonFormRequestClass
{
    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}

?>
