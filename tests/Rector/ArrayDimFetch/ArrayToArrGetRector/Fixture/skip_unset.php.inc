<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class SkipUnset
{
    public function run()
    {
        $array = ['key' => 'value'];

        unset($array['key']);
        unset($array['nested']['key']);
    }
}
