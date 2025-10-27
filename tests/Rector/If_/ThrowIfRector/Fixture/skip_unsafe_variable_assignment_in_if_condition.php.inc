<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

if ($foo = call() && $bar = call()) {
    throw new \Exception($foo, $bar);
}

?>
