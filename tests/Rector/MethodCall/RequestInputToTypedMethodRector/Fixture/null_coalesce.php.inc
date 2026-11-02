<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class NullCoalesce
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = (string) ($request->input('name') ?? null);
        $age = (int) ($request->input('age') ?? null);
        $price = (float) ($request->input('price') ?? null);
        $tags = (array) ($request->input('tags') ?? null);
        $active = (bool) ($request->input('active') ?? null);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class NullCoalesce
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = $request->string('name')->toString();
        $age = $request->integer('age');
        $price = $request->float('price');
        $tags = $request->array('tags');
        $active = (bool) ($request->input('active') ?? null);
    }
}

?>
