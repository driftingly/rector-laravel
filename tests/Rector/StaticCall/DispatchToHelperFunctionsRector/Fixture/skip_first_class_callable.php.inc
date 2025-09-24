<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent;
use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestJob;

TestJob::dispatch(...);
TestJob::dispatchSync(...);
TestEvent::dispatch(...);
