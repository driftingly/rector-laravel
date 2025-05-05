<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

class AppliesToHttpRequests
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

class AppliesToHttpRequests
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);
    }
}

?>
