<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

if ($foo = call() && $bar = call()) {
    report($bar);
}

?>
