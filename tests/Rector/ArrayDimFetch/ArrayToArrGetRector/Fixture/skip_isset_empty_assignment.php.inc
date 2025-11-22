<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class SkipIssetEmptyAssignment
{
    public function run()
    {
        $array = ['key' => 'value', 'nested' => ['inner' => 'data']];

        // Should skip isset checks - no transformation
        if (isset($array['key'])) {
            echo 'exists';
        }

        if (isset($array['nested']['inner'])) {
            echo 'nested exists';
        }

        // Should skip empty checks - no transformation
        if (empty($array['key'])) {
            echo 'empty';
        }

        if (empty($array['nested']['inner'])) {
            echo 'nested empty';
        }

        // Should skip assignment operations - no transformation
        $array['key'] = 'new value';
        $array['nested']['inner'] = 'new data';
        $array['new_key'] = 'added';

        // Should transform - normal read access
        $value = \Illuminate\Support\Arr::get($array, 'key');
        $nested = \Illuminate\Support\Arr::get($array, 'nested.inner');
    }
}

?>
