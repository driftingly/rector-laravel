<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

class FunctionExt1
{
    public function store()
    {
    }
}
trait FunctionTrait{
    public function store()
    {
        $validatedData = request('foo');
    }
}
class FunctionExt2 extends FunctionExt1
{
    use FunctionTrait;
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
trait FunctionTrait{
    public function store()
    {
        $validatedData = request('foo');
    }
}
class FunctionExt2 extends FunctionExt1
{
    use FunctionTrait;
}

?>
