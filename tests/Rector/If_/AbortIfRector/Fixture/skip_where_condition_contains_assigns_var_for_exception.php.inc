<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

if ($foo = call() && $bar = call()) {
    abort($bar);
}

?>
