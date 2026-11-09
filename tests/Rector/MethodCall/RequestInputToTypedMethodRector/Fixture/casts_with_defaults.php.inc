<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastsWithDefaults
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = (string) $request->input('name', '');
        $fallback = (string) $request->input('name', 'Guest');
        $null = (string) $request->input('name', null);
        $age = (int) $request->input('age', 0);
        $page = (int) $request->input('page', self::PER_PAGE);
        $price = (float) $request->input('price', 0);
        $zero = (double) $request->input('price', 0.0);
        $other = (float) $request->input('price', 1.5);
        $tags = (array) $request->input('tags');
        $empty = (array) $request->input('tags', []);
        $terms = explode(',', (string) $request->input('terms'));
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastsWithDefaults
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $name = $request->string('name')->toString();
        $fallback = $request->string('name', 'Guest')->toString();
        $null = $request->string('name', null)->toString();
        $age = $request->integer('age');
        $page = $request->integer('page', self::PER_PAGE);
        $price = $request->float('price');
        $zero = $request->float('price');
        $other = $request->float('price', 1.5);
        $tags = $request->array('tags');
        $empty = $request->array('tags');
        $terms = explode(',', $request->string('terms')->toString());
    }
}

?>
