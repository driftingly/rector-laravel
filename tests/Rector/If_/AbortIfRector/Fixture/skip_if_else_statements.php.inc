<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class SkipIfElseStatements
{
    public function run($condition)
    {
        if ($condition) {
            abort(404);
        } else {
            echo 'hello';
        }

        if ($condition) {
            abort(404);
        } else if ($condition + 1) {
            echo 'hello';
        }

        if ($condition) {
            abort(404);
        } elseif ($condition + 1) {
            echo 'hello';
        }
    }
}
