<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class WithDefaultValues
{
    public function run()
    {
        $array = ['key' => 'value', 'nested' => ['inner' => 'data']];

        // Simple array access with defaults
        $value = $array['key'] ?? 'default';

        // Nested array access with defaults
        $nested = $array['nested']['inner'] ?? 'fallback';

        // Multiple levels with defaults
        $data = $array['level1']['level2']['level3'] ?? null;

        // Integer keys with defaults
        $indexed = $array[0] ?? 'empty';
        $multiIndexed = $array[0][1] ?? 42;
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class WithDefaultValues
{
    public function run()
    {
        $array = ['key' => 'value', 'nested' => ['inner' => 'data']];

        // Simple array access with defaults
        $value = \Illuminate\Support\Arr::get($array, 'key', 'default');

        // Nested array access with defaults
        $nested = \Illuminate\Support\Arr::get($array, 'nested.inner', 'fallback');

        // Multiple levels with defaults
        $data = \Illuminate\Support\Arr::get($array, 'level1.level2.level3', null);

        // Integer keys with defaults
        $indexed = \Illuminate\Support\Arr::get($array, 0, 'empty');
        $multiIndexed = \Illuminate\Support\Arr::get($array, '0.1', 42);
    }
}

?>
