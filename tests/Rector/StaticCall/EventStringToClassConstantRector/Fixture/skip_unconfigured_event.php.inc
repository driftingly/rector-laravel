<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

\Illuminate\Support\Facades\Event::listen('some.unknown.event', function () {});

?>
