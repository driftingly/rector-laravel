<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

use Illuminate\Http\Request;

class SomeClass
{
    public function store()
    {
        Request::validate(['some_attribute' => 'required']);
        request()->validate(['some_attribute' => 'required']);
        request()->get('foo');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

use Illuminate\Http\Request;

class SomeClass
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate(['some_attribute' => 'required']);
        $request->validate(['some_attribute' => 'required']);
        $request->get('foo');
    }
}

?>
