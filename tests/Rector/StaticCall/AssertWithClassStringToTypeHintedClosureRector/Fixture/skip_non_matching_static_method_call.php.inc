<?php

namespace RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Fixture;

\Illuminate\Support\Facades\FooBar::assertDispatched(
    'RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass',
    function ($job) {
        return true;
    }
);

\Illuminate\Support\Facades\Bus::assertDispatchedFoo(
    'RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass',
    function ($job) {
        return true;
    }
);

?>
