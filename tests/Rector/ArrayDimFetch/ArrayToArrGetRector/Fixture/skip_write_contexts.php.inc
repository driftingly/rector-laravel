<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayToArrGetRector\Fixture;

function skipWriteContexts(array $array, array $list)
{
    $array['counter']++;
    --$array['counter'];
    $reference = &$array['key'];
    foreach ($list as $array['key']) {
    }
    [$array['key']] = ['value'];
    sort($array['list']);
    preg_match('/key/', 'key', $array['matches']);
}

?>
