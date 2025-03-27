<?php

namespace RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Fixture;

\Illuminate\Support\Facades\Bus::assertDispatched(
    'FooBar',
    function ($job) {
        return true;
    }
);

?>
