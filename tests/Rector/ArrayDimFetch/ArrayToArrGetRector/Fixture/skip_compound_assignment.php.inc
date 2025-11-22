<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class SkipCompoundAssignment
{
    public function run()
    {
        $array = ['count' => 5, 'value' => 10];

        // Should skip compound assignment operators - no transformation
        $array['count'] += 1;
        $array['value'] -= 2;
        $array['value'] *= 3;
        $array['value'] /= 2;
        $array['value'] %= 4;
        $array['text'] .= ' appended';

        // Should transform - normal read access
        $value = $array['count'];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

class SkipCompoundAssignment
{
    public function run()
    {
        $array = ['count' => 5, 'value' => 10];

        // Should skip compound assignment operators - no transformation
        $array['count'] += 1;
        $array['value'] -= 2;
        $array['value'] *= 3;
        $array['value'] /= 2;
        $array['value'] %= 4;
        $array['text'] .= ' appended';

        // Should transform - normal read access
        $value = \Illuminate\Support\Arr::get($array, 'count');
    }
}

?>
