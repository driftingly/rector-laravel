<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class Function2
{
    public function store()
    {
        $validatedData = request('foo');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class Function2
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validatedData = $request->input('foo');
    }
}

?>
