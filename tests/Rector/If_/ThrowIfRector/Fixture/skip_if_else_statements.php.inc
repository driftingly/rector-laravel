<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class SkipIfElseStatements
{
    public function run($condition)
    {
        if ($condition) {
            throw new Exception();
        } else {
            echo 'hello';
        }

        if ($condition) {
            throw new Exception();
        } else if ($condition + 1) {
            echo 'hello';
        }

        if ($condition) {
            throw new Exception();
        } elseif ($condition + 1) {
            echo 'hello';
        }
    }
}
