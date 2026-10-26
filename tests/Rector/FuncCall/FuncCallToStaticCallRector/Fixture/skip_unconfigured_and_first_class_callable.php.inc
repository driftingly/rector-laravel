<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToStaticCallRector\Fixture;

class SkipUnconfiguredAndFirstClassCallable
{
    public function run(array $array, string $function)
    {
        $keys = array_keys($array);
        $callable = array_get(...);

        return $function($array);
    }
}
