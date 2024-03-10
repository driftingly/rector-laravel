<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestJob;

TestJob::dispatch('param1', 'param2');
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestJob;

dispatch(new RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source\TestJob('param1', 'param2'));
