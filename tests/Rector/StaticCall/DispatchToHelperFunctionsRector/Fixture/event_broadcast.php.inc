<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent;

TestEvent::broadcast('param1', 'param2');
TestEvent::broadcast(...$arguments)->toOthers();
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent;

broadcast(new \RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent('param1', 'param2'));
broadcast(new \RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestEvent(...$arguments))->toOthers();
