<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Fixture;

use Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Source\ChainJob;
use Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Source\Job;

class Fixture
{
    public function run()
    {
        Job::withChain([
            new ChainJob(),
        ])
            ->dispatch()
            ->allOnConnection('redis')
            ->allOnQueue('podcasts');
        Job::withChain([
            new ChainJob(),
        ])
            ->someMethod()
            ->dispatch()
            ->someMethod2()
            ->allOnConnection('redis')
            ->allOnQueue('podcasts');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Fixture;

use Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Source\ChainJob;
use Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Source\Job;

class Fixture
{
    public function run()
    {
        Job::withChain([
            new ChainJob(),
        ])
            ->onQueue('podcasts')
            ->onConnection('redis')
            ->dispatch();
        Job::withChain([
            new ChainJob(),
        ])
            ->someMethod()
            ->onQueue('podcasts')
            ->onConnection('redis')
            ->dispatch()
            ->someMethod2();
    }
}

?>
