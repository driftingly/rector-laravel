<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToStaticCallRector\Fixture;

class FuncCallToStaticCall
{
    public function run(array $array)
    {
        $value = array_get($array, 'key', 'default');

        return STR_SLUG('Hello World');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToStaticCallRector\Fixture;

class FuncCallToStaticCall
{
    public function run(array $array)
    {
        $value = \Illuminate\Support\Arr::get($array, 'key', 'default');

        return \Illuminate\Support\Str::slug('Hello World');
    }
}

?>
