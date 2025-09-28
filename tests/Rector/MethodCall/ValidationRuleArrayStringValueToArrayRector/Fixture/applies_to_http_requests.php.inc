<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

class AppliesToHttpRequests
{
    private int $size = 500;

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'layout' => "required|mimes:png|dimensions:width={$this->size},height={$this->size}",
        ]);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector\Fixture;

class AppliesToHttpRequests
{
    private int $size = 500;

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'layout' => ["required", "mimes:png", "dimensions:width={$this->size},height={$this->size}"],
        ]);
    }
}

?>
