<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent;

TestEvent::dispatch('param1', 'param2');
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent;

event(new RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent('param1', 'param2'));
