<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class SomeFunction
{
    public function store()
    {
        $validatedData = request()->get('foo');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class SomeFunction
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validatedData = $request->get('foo');
    }
}

?>
