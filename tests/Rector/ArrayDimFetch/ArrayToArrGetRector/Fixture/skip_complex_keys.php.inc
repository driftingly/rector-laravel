<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToDataGetRector\Fixture;

class SkipComplexKeys
{
    public function run()
    {
        $array = ['key' => 'value'];
        $dynamicKey = 'key';

        // Should skip variable keys
        $value = $array[$dynamicKey];

        // Should skip method call keys
        $value2 = $array[$this->getKey()];

        // Should skip complex expressions
        $value3 = $array['prefix' . $suffix];

        // Should skip without dimensions
        $emptyDim = $array[];
    }

    private function getKey()
    {
        return 'key';
    }
}

?>
