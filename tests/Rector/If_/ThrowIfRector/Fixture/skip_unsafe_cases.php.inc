<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class SomeClass
{
    public function handle($condition)
    {
        if ($condition) {
            throw new Exception('Some message: ' . foo());
        }
        if ($condition) {
            throw new Exception('Some message: ' . $object->someMethod());
        }
        if ($condition) {
            throw new Exception('Some message: ' . SomeClass::someMethod());
        }
        if ($condition) {
            throw new Exception('Some message: ' . $array[$someOffset]);
        }
    }
}

?>
