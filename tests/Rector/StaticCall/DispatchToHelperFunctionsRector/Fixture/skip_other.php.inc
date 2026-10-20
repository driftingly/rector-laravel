<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\OtherDispatchable;
use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestJob;

OtherDispatchable::dispatch('param1', 'param2');
OtherDispatchable::broadcast('param1', 'param2');
TestJob::broadcast('param1', 'param2');
