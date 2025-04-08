<?php

namespace RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Fixture;

\Illuminate\Support\Facades\Bus::assertDispatched(
    'RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass',
);

?>
