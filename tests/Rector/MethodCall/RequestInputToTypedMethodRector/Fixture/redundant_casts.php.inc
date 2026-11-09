<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class RedundantCasts
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = (string) $request->string('name');
        $age = (int) $request->integer('age');
        $price = (float) $request->float('price');
        $active = (bool) $request->boolean('active');
        $tags = (array) $request->array('tags');
        $differentCast = (int) $request->float('price');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class RedundantCasts
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = $request->string('name')->toString();
        $age = $request->integer('age');
        $price = $request->float('price');
        $active = $request->boolean('active');
        $tags = $request->array('tags');
        $differentCast = (int) $request->float('price');
    }
}

?>
