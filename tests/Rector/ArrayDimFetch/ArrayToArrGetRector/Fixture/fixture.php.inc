<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToDataGetRector\Fixture;

class SomeClass
{
    public function run()
    {
        $array = ['key' => 'value', 'nested' => ['inner' => 'data']];

        // Simple array access
        $value = $array['key'];

        // Nested array access
        $nested = $array['nested']['inner'];

        // Multiple levels
        $data = $array['level1']['level2']['level3'];

        // Integer keys
        $indexed = $array[0];
        $multiIndexed = $array[0][1];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToDataGetRector\Fixture;

class SomeClass
{
    public function run()
    {
        $array = ['key' => 'value', 'nested' => ['inner' => 'data']];

        // Simple array access
        $value = \Illuminate\Support\Arr::get($array, 'key');

        // Nested array access
        $nested = \Illuminate\Support\Arr::get($array, 'nested.inner');

        // Multiple levels
        $data = \Illuminate\Support\Arr::get($array, 'level1.level2.level3');

        // Integer keys
        $indexed = \Illuminate\Support\Arr::get($array, 0);
        $multiIndexed = \Illuminate\Support\Arr::get($array, '0.1');
    }
}

?>
