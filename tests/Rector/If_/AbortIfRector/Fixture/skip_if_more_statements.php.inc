<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class SkipIfMoreStatements
{
    public function run($condition)
    {
        if ($condition) {
            echo 'hello';
            abort(404);
        }
        if ($condition) {
            throw new Exception();
            abort(404);
        }
        if ($condition) {
        }
    }
}

?>
