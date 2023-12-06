<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class SkipIfMoreStatements
{
    public function run($condition)
    {
        if ($condition) {
            echo 'hello';
            throw new Exception();
        }
        if ($condition) {
            throw new Exception();
            echo 'world';
        }
        if ($condition) {
        }
    }
}

?>
