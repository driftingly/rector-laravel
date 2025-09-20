<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class SkipThrowExpressions
{
    public function run()
    {
        $array = ['key' => 'value'];

        // Should skip throw expressions - no transformation
        $value = $array['key'] ?? throw new \Exception('Key not found');

        // Should skip variable keys with throw - no transformation
        $dynamicKey = 'key';
        $value2 = $array[$dynamicKey] ?? throw new \RuntimeException('Missing');

        // Should skip nested throw expressions - no transformation
        $value3 = $array['nested']['inner'] ?? throw new \InvalidArgumentException('Nested missing');
    }
}

?>
