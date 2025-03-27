<?php

namespace RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass;

\Illuminate\Support\Facades\Bus::assertDispatched(
    SomeClass::class,
    function ($job) {
        return true;
    }
);

\Illuminate\Support\Facades\Bus::assertDispatched(
    'RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass',
    function ($job) {
        return true;
    }
);

\Illuminate\Support\Facades\Bus::assertDispatched(
    SomeClass::class,
    fn ($job) => true,
);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass;

\Illuminate\Support\Facades\Bus::assertDispatched(function (\RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass $job) {
    return true;
});

\Illuminate\Support\Facades\Bus::assertDispatched(function (\RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass $job) {
    return true;
});

\Illuminate\Support\Facades\Bus::assertDispatched(fn (\RectorLaravel\Tests\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector\Source\SomeClass $job) => true);

?>
