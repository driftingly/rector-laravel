<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class FunctionExt1
{
    public function store()
    {
    }
}

class FunctionExt2 extends FunctionExt1
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

class FunctionExt1
{
    public function store()
    {
    }
}

class FunctionExt2 extends FunctionExt1
{
    public function store()
    {
        $validatedData = request('foo');
    }
}

?>
