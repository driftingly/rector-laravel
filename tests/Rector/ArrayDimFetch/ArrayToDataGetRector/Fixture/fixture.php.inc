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
        $value = data_get($array, 'key');

        // Nested array access
        $nested = data_get($array, 'nested.inner');

        // Multiple levels
        $data = data_get($array, 'level1.level2.level3');

        // Integer keys
        $indexed = data_get($array, 0);
        $multiIndexed = data_get($array, '0.1');
    }
}

?>
