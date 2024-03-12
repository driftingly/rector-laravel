<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class SkipIfMoreStatements
{
    public function run($condition)
    {
        if ($condition) {
            echo 'hello';
            report(new \Exception());
        }
        if ($condition) {
        }
    }
}

?>
