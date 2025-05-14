<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class SkipIfElseStatements
{
    public function run($condition)
    {
        if ($condition) {
            report(new Exception());
        } else {
            echo 'hello';
        }

        if ($condition) {
            report(new Exception());
        } else if ($condition + 1) {
            echo 'hello';
        }

        if ($condition) {
            report(new Exception());
        } elseif ($condition + 1) {
            echo 'hello';
        }
    }
}
